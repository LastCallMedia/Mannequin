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
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class AssetManagerTest extends TestCase
{
    public function testWritesAssets()
    {
        $root = vfsStream::setup('mannequin-out');
        $manager = new AssetManager(
            new \ArrayIterator([new \SplFileInfo(__FILE__)]),
            __DIR__
        );
        $manager->write($root->url());
        $this->assertTrue($root->hasChild(pathinfo(__FILE__, PATHINFO_BASENAME)));
    }

    public function testGet()
    {
        $barAsset = new SplFileInfo(__DIR__.'/foo/bar', 'foo/', 'foo/bar');
        $manager = new AssetManager(
            new \ArrayIterator([$barAsset]),
            __DIR__
        );
        $this->assertEquals($barAsset, $manager->get('foo/bar'));
    }
}
