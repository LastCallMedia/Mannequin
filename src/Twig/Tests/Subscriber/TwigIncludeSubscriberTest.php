<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Subscriber;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Tests\Subscriber\DiscoverySubscriberTestTrait;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use LastCall\Mannequin\Twig\TwigInspector;
use PHPUnit\Framework\TestCase;

class TwigIncludeSubscriberTest extends TestCase
{
    use DiscoverySubscriberTestTrait;

    public function testRunsDetection()
    {
        $twigSrc = new \Twig_Source('', '', '');

        $inspector = $this->prophesize(TwigInspector::class);
        $inspector->inspectLinked($twigSrc)
            ->willReturn(['bar'])
            ->shouldBeCalled();

        $collection = $this->prophesize(PatternCollection::class);
        $collection->has('bar')
            ->willReturn(true)
            ->shouldBeCalled();
        $collection->get('bar')
            ->willReturn(new TwigPattern('bar', [], $twigSrc))
            ->shouldBeCalled();

        $pattern = new TwigPattern('foo', [], new \Twig_Source('', '', ''));
        $subscriber = new TwigIncludeSubscriber($inspector->reveal());
        $this->dispatchDiscover($subscriber, $pattern, $collection->reveal());
    }
}
