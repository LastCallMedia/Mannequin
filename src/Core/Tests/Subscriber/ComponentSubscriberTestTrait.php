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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Event\ComponentDiscoveryEvent;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Stubs\TestComponent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

trait ComponentSubscriberTestTrait
{
    protected function dispatchDiscover(
        EventSubscriberInterface $subscriber,
        ComponentInterface $component,
        ComponentCollection $collection = null
    ): ComponentDiscoveryEvent {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $collection = $collection ?: new ComponentCollection();

        return $dispatcher->dispatch(
            ComponentEvents::DISCOVER,
            new ComponentDiscoveryEvent($component, $collection)
        );
    }

    protected function dispatchPreRender(
        EventSubscriberInterface $subscriber,
        ComponentCollection $collection = null,
        ComponentInterface $component = null,
        Sample $sample = null
    ) {
        $collection = $collection ?: new ComponentCollection();
        $component = $component ?: new TestComponent('foo');
        $sample = $sample ?: $component->createSample('bar', 'Bar');

        $rendered = new Rendered();
        $event = new RenderEvent(
            $collection,
            $component,
            $sample,
            $rendered,
            true
        );
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);

        return $dispatcher->dispatch(
            ComponentEvents::PRE_RENDER,
            $event
        );
    }
}
