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
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
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
                $this->getTwig()->getLoader(), $this->getTemplateNameIterator()
            ),
        ];
    }

    public function getEngines(): array
    {
        $config = $this->mannequin->getConfig();
        $styles = $config->getStyles();
        $scripts = $config->getScripts();

        return [
            new TwigEngine($this->getTwig(), $styles, $scripts),
        ];
    }

    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $inspector = $this->getInspector();
        $dispatcher->addSubscriber(
            new InlineTwigYamlMetadataSubscriber($inspector)
        );
        $dispatcher->addSubscriber(
            new TwigIncludeSubscriber($inspector)
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
        $mapper = new TemplateNameMapper($this->getTwigRoot());
        foreach ($this->getNamespaces() as $namespace => $paths) {
            $mapper->addNamespace($namespace, $paths);
        }

        return $mapper;
    }

    protected function getInspector()
    {
        return new TwigInspectorCacheDecorator(
            new TwigInspector($this->getTwig()),
            $this->mannequin->getCache()
        );
    }

    abstract protected function getNamespaces(): array;

    abstract protected function getIterator();

    abstract protected function getTwig(): \Twig_Environment;

    abstract protected function getLoader(): \Twig_LoaderInterface;

    abstract protected function getTwigRoot(): string;

    abstract protected function getGlobs(): array;
}
