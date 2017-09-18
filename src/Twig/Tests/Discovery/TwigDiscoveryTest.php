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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use PHPUnit\Framework\TestCase;

class TwigDiscoveryTest extends TestCase
{
    use IdEncoder;

    const FIXTURES_DIR = __DIR__.'/../Resources';

    private function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'form-input.twig' => 'I am twig code',
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

    public function testDiscoversPatternCollection()
    {
        $driver = $this->getDriver($this->getTwig());
        $discovery = new TwigDiscovery($driver, ['form-input.twig']);
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(1, $collection);

        return $collection;
    }

    /**
     * @depends testDiscoversPatternCollection
     */
    public function testDiscoversPattern(ComponentCollection $collection)
    {
        $pattern = $collection->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertInstanceOf(TwigComponent::class, $pattern);

        return $pattern;
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsId(TwigComponent $pattern)
    {
        $this->assertEquals(
            $this->encodeId('form-input.twig'),
            $pattern->getId()
        );
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsName(TwigComponent $pattern)
    {
        $this->assertEquals('form-input.twig', $pattern->getName());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsAliases(TwigComponent $pattern)
    {
        $this->assertEquals(['form-input.twig'], $pattern->getAliases());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsFilename(TwigComponent $pattern)
    {
        $this->assertFalse($pattern->getFile());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsSource(TwigComponent $pattern)
    {
        $source = $pattern->getSource();
        $this->assertInstanceOf(\Twig_Source::class, $source);
        $this->assertEquals('form-input.twig', $source->getName());
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     * @expectedExceptionMessage Unable to load some-nonexistent-file.twig
     */
    public function testThrowsExceptionOnNonLoadableTemplates()
    {
        $driver = $this->getDriver($this->getTwig());
        $discoverer = new TwigDiscovery(
            $driver,
            [['some-nonexistent-file.twig']]
        );
        $discoverer->discover();
    }
}
