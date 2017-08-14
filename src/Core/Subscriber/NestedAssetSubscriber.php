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
use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NestedAssetSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::POST_RENDER => 'bubbleFromVariables',
        ];
    }

    public function bubbleFromVariables(RenderEvent $event)
    {
        $parent = $event->getRendered();

        $this->recurseVars($event->getVariables(), function (Rendered $child) use ($parent) {
            foreach ($child->getAssets()->all() as $asset) {
                $parent->getAssets()->add($asset);
            }
        });
    }

    private function recurseVars(array $variables, $cb)
    {
        foreach ($variables as $variable) {
            if (is_array($variable)) {
                $this->recurseVars($variable, $cb);
            } elseif ($variable instanceof Rendered) {
                $cb($variable);
            }
        }
    }
}
