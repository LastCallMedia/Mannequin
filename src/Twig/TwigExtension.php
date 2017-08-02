<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig;

use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TwigExtension extends AbstractExtension
{
    public function __construct(array $config = [])
    {
        $config += [
            'globs' => [],
            'twig_cache' => false,
            'twig_root' => getcwd(),
            'twig_loader' => function () {
                return new \Twig_Loader_Filesystem([$this['twig_root']], $this['twig_root']);
            },
            'twig' => function () {
                return new \Twig_Environment($this['twig_loader'], [
                        'cache' => $this['twig_cache'],
                        'auto_reload' => true,
                ]);
            },
            'names' => function () {
                return new TwigLoaderIterator($this['twig_loader'], $this['twig_root'], $this['globs']);
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
    public function getEngines(): array
    {
        $config = $this->getConfig();

        return [
            new TwigEngine(
                $this['twig'],
                $config->getStyles(),
                $config->getScripts()
            ),
        ];
    }

    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(
            new InlineTwigYamlMetadataSubscriber($this['inspector'])
        );
        $dispatcher->addSubscriber(
            new TwigIncludeSubscriber($this['inspector'])
        );
    }
}
