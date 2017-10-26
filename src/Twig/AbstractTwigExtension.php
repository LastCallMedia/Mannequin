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
    /**
     * {@inheritdoc}
     */
    public function getDiscoverers(): array
    {
        return [
            new TwigDiscovery(
                $this->getDriver(), $this->getTemplateNameIterator()
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEngines(): array
    {
        return [
            new TwigEngine(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(
            new InlineTwigYamlMetadataSubscriber($this->mannequin->getMetadataParser())
        );
        $dispatcher->addSubscriber(
            new TwigIncludeSubscriber()
        );
    }

    /**
     * Return an iterator that contains a list of twig template names that we
     * want to treat as components.
     *
     * This is formulated by taking an iterator of template filenames, and
     * adding the template name mapper in a way that it gets invoked for each
     * name in turn.
     *
     * @return \LastCall\Mannequin\Core\Iterator\MappingCallbackIterator
     */
    protected function getTemplateNameIterator()
    {
        $iterator = $this->getTemplateFilenameIterator();
        $mapper = $this->getTemplateNameMapper();

        return new MappingCallbackIterator($iterator, $mapper);
    }

    /**
     * Return a callable that knows how to map a filename to a template name.
     *
     * @see \LastCall\Mannequin\Twig\TemplateNameMapper
     */
    protected function getTemplateNameMapper()
    {
        $driver = $this->getDriver();
        $mapper = new TemplateNameMapper($driver->getTwigRoot());
        foreach ($driver->getNamespaces() as $namespace => $paths) {
            $mapper->addNamespace($namespace, $paths);
        }

        return $mapper;
    }

    /**
     * Return the Driver that this extension uses.
     *
     * @return \LastCall\Mannequin\Twig\Driver\TwigDriverInterface
     */
    abstract protected function getDriver(): TwigDriverInterface;

    /**
     * Return a \Traversable object containing the template filenames we want
     * to treat as components.
     *
     * @return \Traversable
     */
    abstract protected function getTemplateFilenameIterator(): \Traversable;
}
