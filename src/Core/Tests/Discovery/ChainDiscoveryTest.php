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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\ExplicitDiscovery;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $pattern1 = $this->getMockPattern('pattern1')->reveal();
        $pattern2 = $this->getMockPattern('pattern2')->reveal();

        $discoverer1 = new ExplicitDiscovery(
            new ComponentCollection([$pattern1])
        );
        $discoverer2 = new ExplicitDiscovery(
            new ComponentCollection([$pattern2])
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
        $pattern1 = $this->getMockPattern('pattern1')->reveal();

        $dispatcher = $this->prophesize(EventDispatcher::class);
        $dispatcher->dispatch(
            PatternEvents::DISCOVER,
            Argument::type(PatternDiscoveryEvent::class)
        )
            ->shouldBeCalled();

        $this->executeDiscovery($pattern1, $dispatcher->reveal());
    }

    public function testAddsProblemForParsingException()
    {
        $pattern = $this->getMockPattern('pattern1');
        $pattern->addProblem('foo')->shouldBeCalled();
        $dispatcher = $this->getExceptionDispatcher();
        $this->executeDiscovery($pattern->reveal(), $dispatcher->reveal());
    }

    public function testLogsParsingException()
    {
        $pattern = $this->getMockPattern('pattern1');
        $pattern->addProblem('foo')->shouldBeCalled();
        $dispatcher = $this->getExceptionDispatcher();
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error('Metadata error for pattern1. foo', Argument::type('array'))->shouldBeCalled();

        $this->executeDiscovery($pattern->reveal(), $dispatcher->reveal(), $logger->reveal());
    }

    private function getMockPattern($name, array $aliases = [])
    {
        $pattern = $this->prophesize(ComponentInterface::class);
        $pattern->getId()->willReturn($name);
        $pattern->getName()->willReturn($name);
        $pattern->getAliases()->willReturn($aliases);
        $pattern->addProblem(Argument::type('string'))->willReturn($pattern);

        return $pattern;
    }

    private function getExceptionDispatcher()
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->dispatch(
            PatternEvents::DISCOVER,
            Argument::type(PatternDiscoveryEvent::class)
        )
            ->willThrow(new TemplateParsingException('foo'));

        return $dispatcher;
    }

    private function executeDiscovery($patterns, EventDispatcherInterface $dispatcher, LoggerInterface $logger = null)
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }
        $discoverer1 = new ExplicitDiscovery(
            new ComponentCollection($patterns)
        );
        $chain = new ChainDiscovery([$discoverer1], $dispatcher, $logger);

        return $chain->discover();
    }
}
