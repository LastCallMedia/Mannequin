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
use LastCall\Mannequin\Core\Event\ComponentDiscoveryEvent;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerAwareInterface;
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
        $component1 = $this->getMockComponent('component1')->reveal();
        $component2 = $this->getMockComponent('component2')->reveal();

        $discoverer1 = new ExplicitDiscovery(
            new ComponentCollection([$component1])
        );
        $discoverer2 = new ExplicitDiscovery(
            new ComponentCollection([$component2])
        );

        $chain = new ChainDiscovery(
            [$discoverer1, $discoverer2],
            new EventDispatcher()
        );
        $merged = $chain->discover();
        $this->assertEquals([$component1, $component2], $merged->getComponents());
    }

    public function testDispatchesEvent()
    {
        $component1 = $this->getMockComponent('component1')->reveal();

        $dispatcher = $this->prophesize(EventDispatcher::class);
        $dispatcher->dispatch(
            ComponentEvents::DISCOVER,
            Argument::type(ComponentDiscoveryEvent::class)
        )
            ->shouldBeCalled();

        $this->executeDiscovery($component1, $dispatcher->reveal());
    }

    public function testAddsProblemForParsingException()
    {
        $component = $this->getMockComponent('component1');
        $component->addProblem('foo')->shouldBeCalled();
        $dispatcher = $this->getExceptionDispatcher();
        $this->executeDiscovery($component->reveal(), $dispatcher->reveal());
    }

    public function testLogsParsingException()
    {
        $component = $this->getMockComponent('component1');
        $component->addProblem('foo')->shouldBeCalled();
        $dispatcher = $this->getExceptionDispatcher();
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error('Metadata error for component1. foo', Argument::type('array'))->shouldBeCalled();

        $this->executeDiscovery($component->reveal(), $dispatcher->reveal(), $logger->reveal());
    }

    public function testCallsSetLogger()
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $discoverer = $this->prophesize(DiscoveryInterface::class);
        $discoverer->willImplement(LoggerAwareInterface::class);
        $discoverer->setLogger($logger)->shouldBeCalled();

        new ChainDiscovery([$discoverer->reveal()], new EventDispatcher(), $logger->reveal());
    }

    private function getMockComponent($name, array $aliases = [])
    {
        $component = $this->prophesize(ComponentInterface::class);
        $component->getId()->willReturn($name);
        $component->getName()->willReturn($name);
        $component->getAliases()->willReturn($aliases);
        $component->addProblem(Argument::type('string'))->willReturn($component);

        return $component;
    }

    private function getExceptionDispatcher()
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->dispatch(
            ComponentEvents::DISCOVER,
            Argument::type(ComponentDiscoveryEvent::class)
        )
            ->willThrow(new TemplateParsingException('foo'));

        return $dispatcher;
    }

    private function executeDiscovery($components, EventDispatcherInterface $dispatcher, LoggerInterface $logger = null)
    {
        if (!is_array($components)) {
            $components = [$components];
        }
        $discoverer1 = new ExplicitDiscovery(
            new ComponentCollection($components)
        );
        $chain = new ChainDiscovery([$discoverer1], $dispatcher, $logger);

        return $chain->discover();
    }
}
