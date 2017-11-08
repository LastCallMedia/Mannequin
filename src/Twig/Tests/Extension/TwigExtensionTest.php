<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Extension;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Driver\SimpleTwigDriver;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\MarkupWrapperSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use LastCall\Mannequin\Twig\TemplateNameMapper;
use LastCall\Mannequin\Twig\TwigExtension;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TwigExtensionTest extends ExtensionTestCase
{
    public function getExtension(): ExtensionInterface
    {
        return new TwigExtension();
    }

    public function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(InlineTwigYamlMetadataSubscriber::class)
        )
            ->shouldBeCalled();
        $dispatcher->addSubscriber(Argument::type(TwigIncludeSubscriber::class))
            ->shouldBeCalled();
        $dispatcher->addSubscriber(Argument::type(MarkupWrapperSubscriber::class))
            ->shouldBeCalled();

        return $dispatcher;
    }

    public function testTwigDiscoveryGetsMappingIterator()
    {
        $root = getcwd();
        $driver = $this->prophesize(TwigDriverInterface::class);
        $driver->getTemplateNameMapper()->shouldBeCalled()->willReturn(new TemplateNameMapper($root));
        $driver = $driver->reveal();
        $extension = new ExposedTwigExtension();
        $extension->setDriver($driver);
        $extension->register($this->getMannequin());
        $inner = new \ArrayIterator([]);
        $outer = new MappingCallbackIterator($inner, new TemplateNameMapper($root));
        $discovery = new TwigDiscovery($driver, $outer);
        $this->assertEquals([$discovery], $extension->getDiscoverers());
    }

    public function testPassesStylesAndScriptsToEngine()
    {
        $driver = new SimpleTwigDriver(getcwd());
        $engine = new TwigEngine($driver, ['foo'], ['bar']);
        $extension = new TwigExtension();
        $config = MannequinConfig::create([
            'styles' => ['foo'],
            'scripts' => ['bar'],
        ]);
        $extension->register($this->getMannequin($config));
        $this->assertEquals([$engine], $extension->getEngines());
    }

    public function getDriverArgumentsTests()
    {
        return [
            [['twig_root' => __DIR__], new SimpleTwigDriver(__DIR__)],
            [['twig_root' => __DIR__, 'twig_options' => ['debug' => true]], new SimpleTwigDriver(__DIR__, ['debug' => true])],
        ];
    }

    /**
     * @dataProvider getDriverArgumentsTests
     */
    public function testCreatesTwigDriverWithArgs($input, SimpleTwigDriver $expected)
    {
        $extension = new ExposedTwigExtension($input);
        $mannequin = $this->getMannequin();
        $expected->setCache(new \Twig_Cache_Filesystem(sys_get_temp_dir().'/mannequin-test/twig'));
        $extension->register($mannequin);
        $this->assertEquals(
            $expected,
            $extension->getTwigDriver()
        );
    }

    public function testAddsTwigNamespaces()
    {
        $extension = new ExposedTwigExtension(['twig_root' => __DIR__]);
        $extension->addTwigPath('foo', '../Resources');
        $mannequin = $this->getMannequin();

        $expected = new SimpleTwigDriver(__DIR__, [], [
            'foo' => ['../Resources'],
        ]);
        $expected->setCache(new \Twig_Cache_Filesystem(sys_get_temp_dir().'/mannequin-test/twig'));
        $extension->register($mannequin);
        $this->assertEquals(
            $expected,
            $extension->getTwigDriver()
        );
    }
}

class ExposedTwigExtension extends TwigExtension
{
    public function getTwigDriver()
    {
        return $this->getDriver();
    }

    public function setDriver(TwigDriverInterface $driver)
    {
        $this->driver = $driver;
    }
}
