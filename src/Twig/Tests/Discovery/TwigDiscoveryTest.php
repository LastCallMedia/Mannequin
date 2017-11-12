<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Discovery;

use LastCall\Mannequin\Core\Component\BrokenComponent;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class TwigDiscoveryTest extends TestCase
{
    use IdEncoder;

    private function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'form-input.twig' => 'I am twig code',
            'broken' => '{% }}',
        ]);

        return new \Twig_Environment($loader, [
            'cache' => false,
            'auto_reload' => true,
        ]);
    }

    private function getDriver(\Twig_Environment $twigEnvironment)
    {
        $driver = $this->prophesize(TwigDriverInterface::class);
        $driver->getTwig()->willReturn($twigEnvironment);

        return $driver->reveal();
    }

    public function testReturnsCollectionOnEmpty()
    {
        $driver = $this->getDriver($this->getTwig());
        $discovery = new TwigDiscovery($driver, []);
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(0, $collection);
    }

    public function testDiscoversCollection()
    {
        $driver = $this->getDriver($this->getTwig());
        $discovery = new TwigDiscovery($driver, ['form-input.twig']);
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(1, $collection);

        return $collection;
    }

    /**
     * @depends testDiscoversCollection
     */
    public function testDiscoversComponent(ComponentCollection $collection)
    {
        $component = $collection->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertInstanceOf(TwigComponent::class, $component);

        return $component;
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsId(TwigComponent $component)
    {
        $this->assertEquals(
            $this->encodeId('form-input.twig'),
            $component->getId()
        );
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsName(TwigComponent $component)
    {
        $this->assertEquals('form-input.twig', $component->getName());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsAliases(TwigComponent $component)
    {
        $this->assertEquals(['form-input.twig'], $component->getAliases());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsFilename(TwigComponent $component)
    {
        $this->assertFalse($component->getFile());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsSource(TwigComponent $component)
    {
        $source = $component->getSource();
        $this->assertInstanceOf(\Twig_Source::class, $source);
        $this->assertEquals('form-input.twig', $source->getName());
    }

    public function testLogsAndReturnsPatternForNonloadableTemplates()
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $driver = $this->getDriver($this->getTwig());
        $discoverer = new TwigDiscovery(
            $driver,
            [['some-nonexistent-twig-file']]
        );
        $discoverer->setLogger($logger->reveal());
        $component = $discoverer->discover()->get($this->encodeId('some-nonexistent-twig-file'));
        $this->assertInstanceOf(BrokenComponent::class, $component);
        $this->assertCount(1, $component->getProblems());
    }

    public function testLoadsBrokenComponent()
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $driver = $this->getDriver($this->getTwig());
        $discoverer = new TwigDiscovery(
            $driver,
            [['broken']]
        );
        $discoverer->setLogger($logger->reveal());
        $component = $discoverer->discover()->get($this->encodeId('broken'));
        $this->assertInstanceOf(BrokenComponent::class, $component);
        $this->assertCount(1, $component->getProblems());
    }
}
