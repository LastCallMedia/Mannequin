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
use Psr\Log\NullLogger;
use Silex\EventListener\LogListener;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

class Application extends \Silex\Application
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
                        $this['config']->getUi(),
                        $this['engine'],
                        $this['variable.resolver'],
                        $this['config']->getAssetMappings()
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
            foreach ($config->getExtensions() as $extension) {
                $extension->register($this);
                $extension->subscribe($this['dispatcher']);
            }

            return $config;
        };
        $this['manifest.builder'] = function () {
            $this->flush();

            return new ManifestBuilder($this['url_generator']);
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
            foreach($this->getExtensions() as $extension) {
                $discoverers = array_merge($discoverers, $extension->getDiscoverers());
            }
            return new ChainDiscovery($discoverers, $this['dispatcher'], $this['logger']);
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
        $this['metadata_parser'] = function() {
            return new YamlMetadataParser();
        };

        $this->register(new ServiceControllerServiceProvider());
        $this['controller.ui'] = function () {
            return new UiController($this['config']->getUi());
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
                $this['engine'],
                $this['config']->getUi(),
                $this['variable.resolver']
            );
        };

        $this->get('/manifest.json', 'controller.manifest:getManifestAction')
            ->bind('manifest');
        $this->get(
            '/m-render/{pattern}/{variant}.html',
            'controller.render:renderAction'
        )->bind('pattern_render');
        $this->get(
            '/m-source/raw/{pattern}.txt',
            'controller.render:renderSourceAction'
        )->bind('pattern_render_source_raw');
        $this->get(
            '/m-source/html/{pattern}/{variant}.txt',
            'controller.render:renderRawAction'
        )->bind('pattern_render_raw');
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

        return parent::boot();
    }

    public function getMetadataParser() : YamlMetadataParser {
        return $this['metadata_parser'];
    }

    public function getConsole(): ConsoleApplication
    {
        return $this['console'];
    }

    /**
     * @return \LastCall\Mannequin\Core\Extension\ExtensionInterface[]
     */
    private function getExtensions()
    {
        return $this['config']->getExtensions();
    }
}
