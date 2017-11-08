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
use LastCall\Mannequin\Twig\Subscriber\MarkupWrapperSubscriber;
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
                $this->getDriver(), $this->getIterator()
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
        $dispatcher->addSubscriber(
            new MarkupWrapperSubscriber()
        );
    }

    /**
     * Return an iterator that contains a list of twig template names that we
     * want to treat as components.
     *
     * This is formulated by taking an iterator of template filenames and
     * allowing the driver to modify them using a mapping callback.  The mapping
     * callback will return the name the driver knows the template by.
     *
     * @return \Traversable
     */
    protected function getIterator(): \Traversable
    {
        return new MappingCallbackIterator(
            $this->getTemplateFilenameIterator(),
            $this->getDriver()->getTemplateNameMapper()
        );
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
