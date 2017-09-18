<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Asset\AssetManager;
use LastCall\Mannequin\Core\Asset\RequestContextContext;
use LastCall\Mannequin\Core\Common\DirectoryCachingInterface;
use LastCall\Mannequin\Core\Console\Application as ConsoleApplication;
use LastCall\Mannequin\Core\Console\Command\DebugCommand;
use LastCall\Mannequin\Core\Console\Command\RenderCommand;
use LastCall\Mannequin\Core\Console\Command\ServerCommand;
use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Engine\DelegatingEngine;
use LastCall\Mannequin\Core\MimeType\ExtensionMimeTypeGuesser;
use LastCall\Mannequin\Core\Ui\Controller\ManifestController;
use LastCall\Mannequin\Core\Ui\Controller\RenderController;
use LastCall\Mannequin\Core\Ui\Controller\UiController;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\NullLogger;
use Silex\Application;
use Silex\EventListener\LogListener;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mannequin extends Application
{
    const APP_NAME = 'Mannequin';

    const APP_VERSION = '0.0.0';

    public function __construct(array $values = [])
    {
        $values += [
            'logger' => function () {
                return new NullLogger();
            },
        ];
        parent::__construct($values);
        $this['console'] = function () {
            $app = new ConsoleApplication(self::APP_NAME, self::APP_VERSION);
            $app->setDispatcher($this['dispatcher']);
            $app->addCommands(
                [
                    new RenderCommand(
                        'render',
                        $this['manifest.builder'],
                        $this['discovery'],
                        $this['ui'],
                        $this['url_generator'],
                        $this['renderer'],
                        $this['asset.manager']
                    ),
                    new ServerCommand(
                        'server',
                        $this['config_file'],
                        $this['autoload_path'],
                        $this['debug']
                    ),
                    new DebugCommand(
                        'debug',
                        $this['manifest.builder'],
                        $this['discovery']
                    ),
                ]
            );

            return $app;
        };

        $this['log.listener'] = function () {
            return new LogListener($this['logger']);
        };
        $this['cache_dir'] = function () {
            return sprintf('%s/mannequin/%s', sys_get_temp_dir(), md5(getcwd()));
        };
        $this['cache'] = function () {
            return new FilesystemAdapter('', 0, $this['cache_dir'].'/cache');
        };

        $this['config'] = function () {
            $filename = $this['config_file'];
            if (!file_exists($filename)) {
                throw new \RuntimeException(
                    sprintf(
                        'Expected config in %s, but the file does not exist.',
                        $filename
                    )
                );
            }
            $config = require $filename;
            if (!$config instanceof ConfigInterface) {
                throw new \RuntimeException(
                    sprintf('Config was not returned from %s.  Did you forget to add a return statement?', $filename)
                );
            }

            return $config;
        };
        $this['manifest.builder'] = function () {
            $this->flush();

            return new ManifestBuilder($this['url_generator']);
        };
        $this['ui'] = function () {
            $ui = $this['config']->getUi();
            if ($ui instanceof DirectoryCachingInterface) {
                $ui->setCacheDir($this->getCacheDir().'/ui');
            }

            return $ui;
        };
        $this['engine'] = function () {
            $engines = [];
            foreach ($this->getExtensions() as $extension) {
                $engines = array_merge($engines, $extension->getEngines());
            }

            return new DelegatingEngine($engines);
        };
        $this['discovery'] = function () {
            $discoverers = [];
            foreach ($this->getExtensions() as $extension) {
                $discoverers = array_merge($discoverers, $extension->getDiscoverers());
            }

            return new ChainDiscovery($discoverers, $this['dispatcher'], $this['logger']);
        };

        $this['asset.package'] = function () {
            return new PathPackage(
                'assets',
                new StaticVersionStrategy(time()),
                new RequestContextContext($this['request_context'])
            );
        };

        $this['asset.manager'] = function () {
            return new AssetManager(
                $this->getConfig()->getAssets(),
                dirname($this['config_file']),
                'assets'
            );
        };
        $this['build_cache'] = function () {
            return sprintf('%s/build', $this['cache_dir']);
        };

        $this['variable.resolver'] = function () {
            $expressionLanguage = new ExpressionLanguage();
            foreach ($this->getExtensions() as $extension) {
                if ($extension instanceof ExpressionFunctionProviderInterface) {
                    $expressionLanguage->registerProvider($extension);
                }
            }

            return new VariableResolver($expressionLanguage);
        };
        $this['metadata_parser'] = function () {
            return new YamlMetadataParser();
        };
        $this['renderer'] = function () {
            return new ComponentRenderer(
                $this['engine'],
                $this['dispatcher']
            );
        };

        $this->register(new ServiceControllerServiceProvider());
        $this['controller.ui'] = function () {
            return new UiController($this['ui'], $this['build_cache']);
        };
        $this['controller.manifest'] = function () {
            return new ManifestController(
                $this['manifest.builder'],
                $this['discovery']->discover()
            );
        };
        $this['controller.render'] = function () {
            $collection = $this['discovery']->discover();

            return new RenderController(
                $collection,
                $this['renderer'],
                $this['ui'],
                $this['asset.manager'],
                $this['build_cache']
            );
        };

        $this->get('/manifest.json', 'controller.manifest:getManifestAction')
            ->bind('manifest');
        $this->get(
            '/m-render/{component}/{variant}.html',
            'controller.render:renderAction'
        )->bind('component_render');
        $this->get(
            '/m-source/raw/{component}.txt',
            'controller.render:renderSourceAction'
        )->bind('component_render_source_raw');
        $this->get(
            '/m-source/html/{component}/{variant}.txt',
            'controller.render:renderRawAction'
        )->bind('component_render_raw');
        $this->match('/{name}', 'controller.ui:staticAction')
            ->bind('static')
            ->value('name', 'index.html')
            ->assert('name', '.+');
    }

    public function boot()
    {
        $this['dispatcher']->addSubscriber($this['log.listener']);
        // Guess file extensions for CSS and JS files.
        MimeTypeGuesser::getInstance()->register(
            new ExtensionMimeTypeGuesser()
        );
        foreach ($this->getExtensions() as $extension) {
            $extension->register($this);
        }
        foreach ($this->getExtensions() as $extension) {
            $extension->subscribe($this['dispatcher']);
        }

        return parent::boot();
    }

    public function getMetadataParser(): YamlMetadataParser
    {
        return $this['metadata_parser'];
    }

    public function getVariableResolver(): VariableResolver
    {
        return $this['variable.resolver'];
    }

    public function getAssetPackage(): PackageInterface
    {
        return $this['asset.package'];
    }

    public function getAssetManager(): AssetManager
    {
        return $this['asset.manager'];
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this['url_generator'];
    }

    public function getRenderer(): ComponentRenderer
    {
        return $this['renderer'];
    }

    public function getConsole(): ConsoleApplication
    {
        return $this['console'];
    }

    public function getConfig(): ConfigInterface
    {
        return $this['config'];
    }

    public function getCache(): CacheItemPoolInterface
    {
        return $this['cache'];
    }

    public function getCacheDir(): string
    {
        return $this['cache_dir'];
    }

    /**
     * @return \LastCall\Mannequin\Core\Extension\ExtensionInterface[]
     */
    private function getExtensions()
    {
        return $this['config']->getExtensions();
    }
}
