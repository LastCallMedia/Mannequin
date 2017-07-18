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
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;

class TwigDiscoveryTest extends TestCase
{
    use IdEncoder;

    const FIXTURES_DIR = __DIR__.'/../Resources';

    public function testReturnsCollectionOnEmpty()
    {
        $loader = $this->prophesize(\Twig_LoaderInterface::class);
        $discovery = new TwigDiscovery($loader->reveal(), []);
        $collection = $discovery->discover();
        $this->assertInstanceOf(PatternCollection::class, $collection);
        $this->assertCount(0, $collection);
    }

    public function testDiscoversPattern()
    {
        $pattern = $this->discoverFixtureCollection()->get(
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
        $this->assertInstanceOf(\Twig_Source::class, $pattern->getSource());
        $source = $pattern->getSource();
        $this->assertEquals('form-input.twig', $source->getName());
        $this->assertEquals(
            realpath(self::FIXTURES_DIR.'/form-input.twig'),
            $source->getPath()
        );
        $this->assertContains('<input', $source->getCode());
    }

    private function discoverFixtureCollection()
    {
        $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
        $discoverer = new TwigDiscovery($loader, [['form-input.twig']]);

        return $discoverer->discover();
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     * @expectedExceptionMessage Unable to load some-nonexistent-file.twig
     */
    public function testThrowsExceptionOnNonLoadableFiles()
    {
        $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
        $discoverer = new TwigDiscovery(
            $loader,
            [['some-nonexistent-file.twig']]
        );
        $discoverer->discover();
    }
}
