<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Config;

use LastCall\Mannequin\Core\Config\ConfigLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use LastCall\Mannequin\Core\MannequinConfig;

class ConfigLoaderTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Expected config in foo, but the file does not exist.
     */
    public function testThrowsExceptionForNonexistantFile()
    {
        ConfigLoader::load('foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /No configuration was returned from \S+\./
     */
    public function testThrowsExceptionForNonReturningFile()
    {
        $root = vfsStream::setup(__FUNCTION__);
        $dir = vfsStream::create([
            'foo' => "<?php\n",
        ], $root);
        ConfigLoader::load($dir->getChild('foo')->url());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Configuration returned from \S+ is not an instance of \S+\./
     */
    public function testThrowsExceptionForAnyOtherReturn()
    {
        $root = vfsStream::setup(__FUNCTION__);
        $dir = vfsStream::create([
            'foo' => "<?php\nreturn new stdClass();",
        ], $root);
        ConfigLoader::load($dir->getChild('foo')->url());
    }

    public function testReturnsConfig()
    {
        $root = vfsStream::setup(__FUNCTION__);
        $dir = vfsStream::create([
            'foo' => "<?php\nreturn new \LastCall\Mannequin\Core\MannequinConfig();",
        ], $root);
        $config = ConfigLoader::load($dir->getChild('foo')->url());
        $this->assertInstanceOf(MannequinConfig::class, $config);
    }

    public function testSetsDocroot()
    {
        $root = vfsStream::setup(__FUNCTION__);
        $dir = vfsStream::create([
            'foo' => "<?php\nreturn new \LastCall\Mannequin\Core\MannequinConfig();",
        ], $root);
        $config = ConfigLoader::load($dir->getChild('foo')->url());
        $this->assertEquals($root->url(), $config->getDocroot());
    }

    public function testSetsCid()
    {
        $root = vfsStream::setup(__FUNCTION__);
        $dir = vfsStream::create([
            'foo' => "<?php\nreturn new \LastCall\Mannequin\Core\MannequinConfig();",
        ], $root);
        $config = ConfigLoader::load($dir->getChild('foo')->url());
        $this->assertEquals(md5($root->getChild('foo')->url()), $config->getCachePrefix());
    }
}
