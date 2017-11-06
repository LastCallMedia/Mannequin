<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Asset;

use LastCall\Mannequin\Core\Asset\AssetManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class AssetManagerTest extends TestCase
{
    public function testReturnsRelativeAssetForGet()
    {
        $barAsset = new \SplFileInfo(__DIR__.'/foo');
        $manager = new AssetManager(
            new \ArrayIterator([$barAsset]),
            __DIR__
        );
        $actual = $manager->get('foo');
        $this->assertInstanceOf(SplFileInfo::class, $actual);
        $this->assertEquals('foo', $actual->getRelativePathname());
    }

    public function testGetMultipleCallsIsConsistent()
    {
        $fooAsset = new \SplFileInfo(__DIR__.'/foo');
        $barAsset = new \SplFileInfo(__DIR__.'/bar');
        $manager = new AssetManager(
            new \ArrayIterator([$fooAsset, $barAsset]),
            __DIR__
        );
        $first = $manager->get('foo');
        $second = $manager->get('foo');
        $this->assertEquals($first, $second);
    }

    public function testReturnsIteratorContainingRelativeAsset()
    {
        $barAsset = new \SplFileInfo(__DIR__.'/foo');
        $manager = new AssetManager(
            new \ArrayIterator([$barAsset]),
            __DIR__
        );
        $expected = new SplFileInfo(__DIR__.'/foo', '', 'foo');
        $this->assertInstanceOf(\Traversable::class, $manager);
        $this->assertEquals([$expected], iterator_to_array($manager));
    }
}
