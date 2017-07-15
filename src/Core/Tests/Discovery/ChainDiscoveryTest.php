<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Discovery;

use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\ExplicitDiscovery;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ChainDiscoveryTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Discoverer must implement
     *   LastCall\Mannequin\Core\Discovery\DiscoveryInterface
     */
    public function testInvalidDiscoverer()
    {
        $discoverer = new \stdClass();
        new ChainDiscovery([$discoverer], new EventDispatcher());
    }

    public function testCallsDiscoverers()
    {
        $discoverer = $this->prophesize(DiscoveryInterface::class);
        $discoverer->discover()
            ->shouldBeCalled();

        $chain = new ChainDiscovery(
            [$discoverer->reveal()],
            new EventDispatcher()
        );
        $chain->discover();
    }

    public function testMergesCollection()
    {
        $pattern1Mock = $this->prophesize(PatternInterface::class);
        $pattern1Mock->getId()->willReturn('pattern1');
        $pattern1Mock->getAliases()->willReturn(['pattern/1']);
        $pattern1 = $pattern1Mock->reveal();

        $pattern2Mock = $this->prophesize(PatternInterface::class);
        $pattern2Mock->getId()->willReturn('pattern2');
        $pattern2Mock->getAliases()->willReturn(['pattern/2']);
        $pattern2 = $pattern2Mock->reveal();

        $discoverer1 = new ExplicitDiscovery(
            new PatternCollection([$pattern1])
        );
        $discoverer2 = new ExplicitDiscovery(
            new PatternCollection([$pattern2])
        );

        $chain = new ChainDiscovery(
            [$discoverer1, $discoverer2],
            new EventDispatcher()
        );
        $merged = $chain->discover();
        $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
    }

    public function testDispatchesEvent()
    {
        $pattern1Mock = $this->prophesize(PatternInterface::class);
        $pattern1Mock->getId()->willReturn('pattern1');
        $pattern1Mock->getAliases()->willReturn(['pattern/1']);
        $pattern1 = $pattern1Mock->reveal();

        $dispatcher = $this->prophesize(EventDispatcher::class);
        $dispatcher->dispatch(
            PatternEvents::DISCOVER,
            Argument::type(PatternDiscoveryEvent::class)
        )
            ->shouldBeCalled();

        $discoverer1 = new ExplicitDiscovery(
            new PatternCollection([$pattern1])
        );
        $chain = new ChainDiscovery([$discoverer1], $dispatcher->reveal());
        $chain->discover();
    }
}
