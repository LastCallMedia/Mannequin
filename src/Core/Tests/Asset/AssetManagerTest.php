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

class AssetManagerTest extends TestCase
{
    public function testWritesAssets()
    {
        $root = vfsStream::setup('mannequin-out');
        $manager = new AssetManager(
            new \ArrayIterator([new \SplFileInfo(__FILE__)]),
            __DIR__,
            ''
        );
        $manager->write($root->url());
        $this->assertTrue($root->hasChild(pathinfo(__FILE__, PATHINFO_BASENAME)));
    }

    public function testWritesAssetsToSubdir()
    {
        $root = vfsStream::setup('mannequin-out');
        $manager = new AssetManager(
            new \ArrayIterator([new \SplFileInfo(__FILE__)]),
            __DIR__,
            'assets'
        );
        $manager->write($root->url());
        $this->assertTrue($root->hasChild('assets/'.pathinfo(__FILE__, PATHINFO_BASENAME)));
    }
}
