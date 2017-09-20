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

use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VariableResolverSubscriber implements EventSubscriberInterface
{
    private $resolver;
    private $mannequin;

    public static function getSubscribedEvents()
    {
        return [
            ComponentEvents::PRE_RENDER => 'resolveVariables',
        ];
    }

    public function __construct(VariableResolver $resolver, Mannequin $mannequin)
    {
        $this->resolver = $resolver;
        $this->mannequin = $mannequin;
    }

    public function resolveVariables(RenderEvent $event)
    {
        $sample = $event->getSample();

        $variables = $this->resolver->resolve($sample->getVariables(), [
            'mannequin' => $this->mannequin,
            'collection' => $event->getCollection(),
            'component' => $event->getComponent(),
            'sample' => $sample,
        ]);

        $event->setVariables($variables);
    }
}
