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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Html\Component\HtmlComponent;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class HtmlDiscoveryTest extends TestCase
{
    use IdEncoder;

    private function discoverFixtureCollection()
    {
        $discoverer = new HtmlDiscovery(new \ArrayIterator([
            __DIR__.'/../Resources/button.html',
        ]));

        return $discoverer->discover();
    }

    public function testDiscoversComponent()
    {
        $id = __DIR__.'/../Resources/button.html';
        $component = $this->discoverFixtureCollection()->get(
            $this->encodeId($id)
        );
        $this->assertInstanceOf(HtmlComponent::class, $component);

        return $component;
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsId(HtmlComponent $component)
    {
        $id = __DIR__.'/../Resources/button.html';
        $this->assertEquals($this->encodeId($id), $component->getId());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testHasDefaultTags(HtmlComponent $component)
    {
        $this->assertArraySubset([
            'group' => 'Unknown',
            'source_format' => 'html',
        ], $component->getMetadata());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsName(HtmlComponent $component)
    {
        $name = __DIR__.'/../Resources/button.html';
        $this->assertEquals($name, $component->getName());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsFile(HtmlComponent $component)
    {
        $file = __DIR__.'/../Resources/button.html';
        $this->assertEquals($file, $component->getFile()->getPathname());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsAliases(HtmlComponent $component)
    {
        $file = __DIR__.'/../Resources/button.html';
        $this->assertEquals([$file], $component->getAliases());
    }

    public function testAcceptsSplFileInfo()
    {
        $discovery = new HtmlDiscovery(new \ArrayIterator([
            new SplFileInfo('/foo/bar/baz', 'bar', 'bar/baz'),
            new \SplFileInfo('/baz/bar/foo'),
        ]));
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(2, $collection);

        return $collection;
    }

    /**
     * @depends testAcceptsSplFileInfo
     */
    public function testUsesRelativePathFromRelativeSplFileInfo(ComponentCollection $collection)
    {
        $this->assertTrue($collection->has($this->encodeId('bar/baz')));

        return $collection->get($this->encodeId('bar/baz'));
    }

    /**
     * @depends testUsesRelativePathFromRelativeSplFileInfo
     */
    public function testSetsSplFileInfoFromRelativeSplFileInfo(HtmlComponent $component)
    {
        $this->assertEquals(
            new SplFileInfo('/foo/bar/baz', 'bar', 'bar/baz'),
            $component->getFile()
        );
    }

    /**
     * @depends testAcceptsSplFileInfo
     */
    public function testSetsAbsoluteIdFromAbsoluteSplFileInfo(ComponentCollection $collection)
    {
        $this->assertTrue($collection->has($this->encodeId('/baz/bar/foo')));

        return $collection->get($this->encodeId('/baz/bar/foo'));
    }

    /**
     * @depends testSetsAbsoluteIdFromAbsoluteSplFileInfo
     */
    public function testSetsSplFileInfoFromSplFileInfo(HtmlComponent $component)
    {
        $this->assertEquals(
            new \SplFileInfo('/baz/bar/foo'),
            $component->getFile()
        );
    }

    public function testReturnsCollectionOnEmpty()
    {
        $discovery = new HtmlDiscovery(new \ArrayIterator([]));
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(0, $collection);
    }
}
