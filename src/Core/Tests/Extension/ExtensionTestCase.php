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

use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Variable\ResolverInterface;
use LastCall\Mannequin\Core\Variable\SetResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ExtensionTestCase extends TestCase
{
    public function testAttachDispatcher()
    {
        $extension = $this->getExtension();
        $extension->setConfig($this->getConfig());
        $extension->attachToDispatcher(
            $this->getDispatcherProphecy()->reveal()
        );
    }

    abstract public function getExtension(): ExtensionInterface;

    public function getConfig(): ConfigInterface
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getCacheDir()->willReturn('');
        $config->getDispatcher()->willReturn(new EventDispatcher());
        $config->getVariableResolver()->willReturn(new SetResolver());
        $config->getStyles()->willReturn([]);
        $config->getScripts()->willReturn([]);

        return $config->reveal();
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(EventSubscriberInterface::class)
        )->shouldNotBeCalled();

        return $dispatcher;
    }

    public function testGetRenderers()
    {
        $extension = $this->getExtension();
        $extension->setConfig($this->getConfig());
        $this->assertContainsOnlyInstancesOf(
            EngineInterface::class,
            $extension->getRenderers()
        );
    }

    public function testHasVariableResolvers()
    {
        $extension = $this->getExtension();
        $extension->setConfig($this->getConfig());
        $this->assertContainsOnlyInstancesOf(
            ResolverInterface::class,
            $extension->getVariableResolvers()
        );
    }

    public function testHasDiscoverers()
    {
        $extension = $this->getExtension();
        $extension->setConfig($this->getConfig());
        $this->assertContainsOnlyInstancesOf(
            DiscoveryInterface::class,
            $extension->getDiscoverers()
        );
    }
}
