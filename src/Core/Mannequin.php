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
use LastCall\Mannequin\Core\Asset\AssetManagerInterface;
use LastCall\Mannequin\Core\Asset\RequestContextContext;
use LastCall\Mannequin\Core\Config\ConfigInterface;
use LastCall\Mannequin\Core\DependencyInjection\ContainerInterface;
use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Engine\DelegatingEngine;
use LastCall\Mannequin\Core\MimeType\ExtensionMimeTypeGuesser;
use LastCall\Mannequin\Core\Provider\ServiceControllerServiceProvider;
use LastCall\Mannequin\Core\Snapshot\Camera;
use LastCall\Mannequin\Core\Snapshot\CameraInterface;
use LastCall\Mannequin\Core\Ui\Controller\ManifestController;
use LastCall\Mannequin\Core\Ui\Controller\RenderController;
use LastCall\Mannequin\Core\Ui\Controller\StaticFileController;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\NullLogger;
use LastCall\Mannequin\Core\EventListener\ExceptionListener;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;


/**
 * DI/Kernel class for Mannequin.
 *
 * NB: This class is likely to change rapidly.  Wherever possible, type hint
 * to ContainerInterface rather than this class, and never rely on the
 * Silex/Pimple ArrayAccess getters/setters.
 */

class Mannequin extends Application implements ContainerInterface {

    public function __construct(ConfigInterface $config, array $values = [])
    {
        $values += [
            'logger' => function () {
                return new NullLogger();
            },
        ];
        parent::__construct($values);
        $this['config'] = $config;
        $this['commands'] = function () use ($config) {
            $commands = [];
            foreach ($this->getExtensions() as $extension) {
                $commands = array_merge($commands, $extension->getCommands());
            }

            return $commands;
        };

        $this['log.listener'] = function () {
            return new ExceptionListener($this['logger']);
        };
        $this['cache_dir'] = function () use ($config) {
            return sprintf('%s/mannequin/%s', sys_get_temp_dir(), $config->getCachePrefix());
        };
        $this['cache'] = function () {
            return new FilesystemAdapter('', 0, $this['cache_dir'].'/cache');
        };

        $this['manifest.builder'] = function () {
            $this->flush();

            return new ManifestBuilder($this['url_generator']);
        };
        $this['ui'] = function () {
            return $this['config']->getUi();
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
                '',
                new StaticVersionStrategy(time()),
                new RequestContextContext($this['request_context'])
            );
        };

        $this['asset.manager'] = function () use ($config) {
            return new AssetManager(
                $config->getAssets(),
                $config->getDocroot()
            );
        };

        $this['variable.resolver'] = function () {
            $expressionLanguage = new ExpressionLanguage();
            foreach ($this->getExtensions() as $extension) {
                if ($extension instanceof ExpressionFunctionProviderInterface) {
                    $expressionLanguage->registerProvider($extension);
                }
            }

            return new VariableResolver($expressionLanguage, $this);
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
        $this['camera'] = function () {
            return new Camera(
                $this->getManifestBuilder(),
                $this->getRenderer(),
                $this->getUrlGenerator(),
                $this->getConfig()->getUi(),
                $this['logger']
            );
        };

        $this->register(new ServiceControllerServiceProvider());
        $this['controller.static'] = function () {
            return new StaticFileController($this['ui'], $this['asset.manager']);
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
                $this['ui']
            );
        };

        $this->get('/', 'controller.static:indexAction')->bind('index');
        $this->get('/manifest.json', 'controller.manifest:getManifestAction')
            ->bind('manifest');
        $this->get(
            '/m-render/{component}/{sample}.html',
            'controller.render:renderAction'
        )->bind('component_render');
        $this->get(
            '/m-source/raw/{component}.txt',
            'controller.render:renderSourceAction'
        )->bind('component_render_source_raw');
        $this->get(
            '/m-source/html/{component}/{sample}.txt',
            'controller.render:renderRawAction'
        )->bind('component_render_raw');
        $this->match('/{name}', 'controller.static:staticAction')
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

    public function getAssetManager(): AssetManagerInterface
    {
        return $this['asset.manager'];
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this['url_generator'];
    }

    public function getManifestBuilder(): ManifestBuilder
    {
        return $this['manifest.builder'];
    }

    public function getDiscovery(): DiscoveryInterface
    {
        return $this['discovery'];
    }

    public function getRenderer(): ComponentRenderer
    {
        return $this['renderer'];
    }

    public function getCamera(): CameraInterface
    {
        return $this['camera'];
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

    public function isDebug(): bool
    {
        return (bool) $this['debug'];
    }

    public function has($id)
    {
        return $this->offsetExists($id);
    }

    /**
     * @return \LastCall\Mannequin\Core\Extension\ExtensionInterface[]
     */
    private function getExtensions()
    {
        return $this['config']->getExtensions();
    }
}
