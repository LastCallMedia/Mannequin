<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Subscriber;

use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Wraps `Rendered` objects in a `\Twig_Markup` object before rendering.
 *
 * This subscriber runs after variables have been resolved, and ensures
 * that any variables that should contain safe markup get wrapped in an
 * wrapper that prevents the markup from being escaped by Twig.
 */
class MarkupWrapperSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            // Runs after variable resolution.
            ComponentEvents::PRE_RENDER => ['convertRenderedToMarkup', -5],
        ];
    }

    public function convertRenderedToMarkup(RenderEvent $event)
    {
        if ($event->getComponent() instanceof TwigComponent) {
            $variables = $this->upcastRendered($event->getVariables());
            $event->setVariables($variables);
        }
    }

    private function upcastRendered(array $variables)
    {
        $return = [];

        foreach ($variables as $key => $value) {
            if ($value instanceof Rendered) {
                $value = new \Twig_Markup($value->getMarkup(), 'UTF-8');
            } elseif (is_array($value)) {
                $value = $this->upcastRendered($value);
            }
            $return[$key] = $value;
        }

        return $return;
    }
}
