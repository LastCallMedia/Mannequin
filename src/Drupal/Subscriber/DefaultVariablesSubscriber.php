<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Subscriber;

use Drupal\Core\Template\Attribute;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Drupal\Component\DrupalTwigComponent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DefaultVariablesSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ComponentEvents::PRE_RENDER => ['addDefaultVariables'],
        ];
    }

    public function addDefaultVariables(RenderEvent $event)
    {
        if ($event->getComponent() instanceof DrupalTwigComponent) {
            $variables = $event->getVariables();
            $variables += [
                'attributes' => new Attribute(),
                'title_attributes' => new Attribute(),
                'content_attributes' => new Attribute(),
                'title_prefix' => [],
                'title_suffix' => [],
                'db_is_active' => true,
                'is_admin' => false,
                'logged_in' => false,
            ];
            $event->setVariables($variables);
        }
    }
}
