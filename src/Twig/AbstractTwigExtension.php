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
use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;

abstract class AbstractTwigExtension extends AbstractExtension
{
    public function getDiscoverers(): array
    {
        return [
            new TwigDiscovery(
                $this->getDriver(), $this->getTemplateNameIterator()
            ),
        ];
    }

    public function getEngines(): array
    {
        return [
            new TwigEngine(),
        ];
    }

    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $inspector = $this->getInspector();
        $dispatcher->addSubscriber(
            new InlineTwigYamlMetadataSubscriber($inspector)
        );
        $dispatcher->addSubscriber(
            new TwigIncludeSubscriber()
        );
    }

    protected function getTemplateNameIterator()
    {
        $iterator = $this->getIterator();
        $mapper = $this->getTemplateNameMapper();

        return new MappingCallbackIterator($iterator, $mapper);
    }

    protected function getTemplateNameMapper()
    {
        $driver = $this->getDriver();
        $mapper = new TemplateNameMapper($driver->getTwigRoot());
        foreach ($driver->getNamespaces() as $namespace => $paths) {
            $mapper->addNamespace($namespace, $paths);
        }

        return $mapper;
    }

    protected function getInspector()
    {
        return new TwigInspectorCacheDecorator(
            new TwigInspector(),
            $this->mannequin->getCache()
        );
    }

    abstract protected function getDriver(): TwigDriverInterface;

    abstract protected function getIterator(): \Traversable;
}
