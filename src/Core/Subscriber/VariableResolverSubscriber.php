<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Subscriber;

use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VariableResolverSubscriber implements EventSubscriberInterface
{
    private $resolver;

    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::PRE_RENDER => 'resolveVariables',
        ];
    }

    public function __construct(VariableResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolveVariables(RenderEvent $event)
    {
        $variant = $event->getVariant();

        $variables = $this->resolver->resolve($variant->getVariables(), [
            'collection' => $event->getCollection(),
            'pattern' => $event->getPattern(),
            'variant' => $variant,
            'rendered' => $event->getRendered(),
        ]);

        $event->setVariables($variables);
    }
}
