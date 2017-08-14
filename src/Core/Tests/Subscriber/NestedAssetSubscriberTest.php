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

use Assetic\Asset\FileAsset;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Subscriber\NestedAssetSubscriber;
use PHPUnit\Framework\TestCase;

class NestedAssetSubscriberTest extends TestCase
{
    public function testBubblesAssetsFromVariables()
    {
        $parent = new Rendered();
        $child = new Rendered();
        $child->getAssets()->add(new FileAsset(__FILE__));

        $event = $this->prophesize(RenderEvent::class);
        $event->getRendered()->willReturn($parent);
        $event->getVariables()->willReturn(['child' => $child]);
        $subscriber = new NestedAssetSubscriber();
        $subscriber->bubbleFromVariables($event->reveal());
        $this->assertEquals($parent->getAssets(), $child->getAssets());
    }
}
