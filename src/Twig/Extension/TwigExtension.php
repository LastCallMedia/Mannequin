<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Extension;

use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Mapper\FilesystemLoaderMapper;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use LastCall\Mannequin\Twig\TwigInspector;
use LastCall\Mannequin\Twig\TwigInspectorCacheDecorator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TwigExtension extends AbstractExtension
{
    public function __construct(array $config = [])
    {
        $config += [
            'globs' => [],
            'twig_paths' => [
                \Twig_Loader_Filesystem::MAIN_NAMESPACE => [getcwd()],
            ],
            'finder' => function () {
                throw new \RuntimeException('Finder must be configured.');
            },
            'twig_cache_dir' => null,
            'twig' => function () {
                $loader = new \Twig_Loader_Filesystem();
                foreach ($this['twig_paths'] as $namespace => $paths) {
                    foreach ($paths as $path) {
                        $loader->addPath($path, $namespace);
                    }
                }

                return new \Twig_Environment(
                    $loader, [
                    'cache' => $this['twig_cache_dir'],
                    'auto_reload' => true,
                ]
                );
            },
            'filename_mapper' => function () {
                $mapper = new FilesystemLoaderMapper();
                foreach ($this['twig_paths'] as $namespace => $paths) {
                    foreach ($paths as $path) {
                        $mapper->addPath($path, $namespace);
                    }
                }

                return $mapper;
            },
            'names' => function () {
                return new MappingCallbackIterator(
                    $this['finder'],
                    $this['filename_mapper']
                );
            },
        ];
        parent::__construct($config);
        $this['inspector'] = function () {
            return new TwigInspectorCacheDecorator(
                new TwigInspector($this['twig']),
                $this->getConfig()->getCache()
            );
        };
        $this['discovery'] = function () {
            return new TwigDiscovery(
                $this['twig']->getLoader(), $this['names']
            );
        };
    }

    public function getDiscoverers(): array
    {
        return [$this['discovery']];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderers(): array
    {
        $config = $this->getConfig();

        return [
            new TwigEngine(
                $this['twig'],
                $config->getVariableResolver(),
                $config->getStyles(),
                $config->getScripts()
            ),
        ];
    }

    public function attachToDispatcher(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(
            new InlineTwigYamlMetadataSubscriber($this['inspector'])
        );
        $dispatcher->addSubscriber(
            new TwigIncludeSubscriber($this['inspector'])
        );
    }
}
