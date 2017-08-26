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

use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
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
        $this->assertInstanceOf(PatternCollection::class, $collection);
        $this->assertCount(0, $collection);
    }

    public function testDiscoversPatternCollection()
    {
        $driver = $this->getDriver($this->getTwig());
        $discovery = new TwigDiscovery($driver, ['form-input.twig']);
        $collection = $discovery->discover();
        $this->assertInstanceOf(PatternCollection::class, $collection);
        $this->assertCount(1, $collection);

        return $collection;
    }

    /**
     * @depends testDiscoversPatternCollection
     */
    public function testDiscoversPattern(PatternCollection $collection)
    {
        $pattern = $collection->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertInstanceOf(TwigPattern::class, $pattern);

        return $pattern;
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsId(TwigPattern $pattern)
    {
        $this->assertEquals(
            $this->encodeId('form-input.twig'),
            $pattern->getId()
        );
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsName(TwigPattern $pattern)
    {
        $this->assertEquals('form-input.twig', $pattern->getName());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsAliases(TwigPattern $pattern)
    {
        $this->assertEquals(['form-input.twig'], $pattern->getAliases());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsSource(TwigPattern $pattern)
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
