<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Subscriber;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

trait DiscoverySubscriberTestTrait
{
    protected function dispatchDiscover(
        EventSubscriberInterface $subscriber,
        PatternInterface $pattern,
        PatternCollection $collection = null
    ): PatternDiscoveryEvent {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $collection = $collection ?: new PatternCollection();

        return $dispatcher->dispatch(
            PatternEvents::DISCOVER,
            new PatternDiscoveryEvent($pattern, $collection)
        );
    }
}
