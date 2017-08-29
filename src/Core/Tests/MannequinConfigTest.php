<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\Cache\NullCacheItemPool;
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Ui\UiInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

class MannequinConfigTest extends TestCase
{
    public function testCanUseCreate()
    {
        $config = MannequinConfig::create();
        $this->assertInstanceOf(MannequinConfig::class, $config);
    }

    public function testHasCoreExtension()
    {
        $config = new MannequinConfig();
        $extensions = $config->getExtensions();
        $this->assertCount(1, $extensions);
        $this->assertInstanceOf(CoreExtension::class, reset($extensions));
    }

    public function testHasDefaultCache()
    {
        $config = new MannequinConfig();
        $this->assertEquals(
            new NullCacheItemPool(),
            $config->getCache()
        );
    }

    public function testCacheCanBeOverridden()
    {
        $cache = $this->prophesize(CacheItemPoolInterface::class)->reveal();
        $config = new MannequinConfig(['cache' => $cache]);
        $this->assertEquals($cache, $config->getCache());
    }

    public function testHasDefaultGlobalCss()
    {
        $config = new MannequinConfig();
        $this->assertEquals([], $config->getGlobalCss());
    }

    public function testCanOverrideGlobalCss()
    {
        $config = new MannequinConfig();
        $config->setGlobalCss(['foo']);
        $this->assertEquals(['foo'], $config->getGlobalCss());
    }

    public function testHasDefaultGlobalJs()
    {
        $config = new MannequinConfig();
        $this->assertEquals([], $config->getGlobalJs());
    }

    public function testCanOverrideGlobalJs()
    {
        $config = new MannequinConfig();
        $config->setGlobalJs(['foo']);
        $this->assertEquals(['foo'], $config->getGlobalJs());
    }

    public function testHasDefaultUi()
    {
        $config = new MannequinConfig();
        $this->assertInstanceOf(UiInterface::class, $config->getUi());
    }

    public function getCanOverrideUi()
    {
        $ui = $this->prophesize(UiInterface::class);
        $config = new MannequinConfig(['ui' => $ui->reveal()]);
        $this->assertEquals($ui, $config->getUi());
    }
}
