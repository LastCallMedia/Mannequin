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
        $twig = $this->prophesize(\Twig_Environment::class);
        $p1 = $this->prophesize(TwigPattern::class);
        $p2 = $this->prophesize(TwigPattern::class);
        $source = $this->prophesize(\Twig_Source::class);
        $p1->getTwig()->willReturn($twig);
        $p1->getSource()->willReturn($source);

        $collection = $this->prophesize(PatternCollection::class);
        $collection->has('bar')
            ->willReturn(true);
        $collection->get('bar')
            ->willReturn($p2);

        $inspector = $this->prophesize(TwigInspector::class);
        $inspector->inspectLinked($twig, $source)
            ->willReturn(['bar'])
            ->shouldBeCalled();

        $p1->addUsedPattern($p2)->shouldBeCalled();
        $subscriber = new TwigIncludeSubscriber($inspector->reveal());
        $this->dispatchDiscover($subscriber, $p1->reveal(), $collection->reveal());
    }
}
