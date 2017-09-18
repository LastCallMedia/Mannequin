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

    public function testReturnsCollectionOnEmpty()
    {
        $discovery = new HtmlDiscovery([]);
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(0, $collection);
    }
}
