<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Extension;

use LastCall\Mannequin\Core\Application;
use LastCall\Mannequin\Core\Cache\NullCacheItemPool;
use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\YamlMetadataParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ExtensionTestCase extends TestCase
{
    protected function getNullCache()
    {
        return new NullCacheItemPool();
    }

    public function testSubscribe()
    {
        $extension = $this->getExtension();
        $extension->register($this->getMannequin());
        $extension->subscribe(
            $this->getDispatcherProphecy()->reveal()
        );
    }

    public function testGetRenderers()
    {
        $extension = $this->getExtension();
        $extension->register($this->getMannequin());
        $engines = $extension->getEngines();
        $this->assertContainsOnlyInstancesOf(
            EngineInterface::class,
            $engines
        );

        return $engines;
    }

    public function testHasDiscoverers()
    {
        $extension = $this->getExtension();
        $extension->register($this->getMannequin());
        $discoverers = $extension->getDiscoverers();
        $this->assertContainsOnlyInstancesOf(
            DiscoveryInterface::class,
            $discoverers
        );

        return $discoverers;
    }

    abstract public function getExtension(): ExtensionInterface;

    public function getConfig(): ConfigInterface
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getCache()->willReturn($this->getNullCache());
        $config->getStyles()->willReturn([]);
        $config->getScripts()->willReturn([]);

        return $config->reveal();
    }

    public function getMannequin(ConfigInterface $config = null): Application
    {
        $mannequin = $this->prophesize(Application::class);
        $mannequin->getMetadataParser()->willReturn(new YamlMetadataParser());
        $mannequin->getConfig()->willReturn($config ?? $this->getConfig());

        return $mannequin->reveal();
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(EventSubscriberInterface::class)
        )->shouldNotBeCalled();

        return $dispatcher;
    }
}
