<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Tests\Discovery;

use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;

class HtmlDiscoveryTest extends TestCase
{
    use IdEncoder;

    private function discoverFixtureCollection()
    {
        $discoverer = new HtmlDiscovery([
            __DIR__.'/../Resources/button.html',
        ]);

        return $discoverer->discover();
    }

    public function testDiscoversPattern()
    {
        $id = __DIR__.'/../Resources/button.html';
        $pattern = $this->discoverFixtureCollection()->get(
            $this->encodeId($id)
        );
        $this->assertInstanceOf(HtmlPattern::class, $pattern);

        return $pattern;
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsId(HtmlPattern $pattern)
    {
        $id = __DIR__.'/../Resources/button.html';
        $this->assertEquals($this->encodeId($id), $pattern->getId());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testHasDefaultTags(HtmlPattern $pattern)
    {
        $this->assertArraySubset([
            'group' => 'Unknown',
            'source_format' => 'html',
        ], $pattern->getMetadata());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsName(HtmlPattern $pattern)
    {
        $name = __DIR__.'/../Resources/button.html';
        $this->assertEquals($name, $pattern->getName());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsFile(HtmlPattern $pattern)
    {
        $file = __DIR__.'/../Resources/button.html';
        $this->assertEquals($file, $pattern->getFile()->getPathname());
    }

    /**
     * @depends testDiscoversPattern
     */
    public function testSetsAliases(HtmlPattern $pattern)
    {
        $file = __DIR__.'/../Resources/button.html';
        $this->assertEquals([$file], $pattern->getAliases());
    }

    public function testReturnsCollectionOnEmpty()
    {
        $discovery = new HtmlDiscovery([]);
        $collection = $discovery->discover();
        $this->assertInstanceOf(PatternCollection::class, $collection);
        $this->assertCount(0, $collection);
    }
}
