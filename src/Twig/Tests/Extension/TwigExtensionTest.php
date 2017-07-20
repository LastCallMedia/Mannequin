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
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use LastCall\Mannequin\Twig\TwigExtension;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class TwigExtensionTest extends ExtensionTestCase
{
    public function getExtension(): ExtensionInterface
    {
        return new TwigExtension(
            [
                'finder' => Finder::create()->in(__DIR__),
            ]
        );
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

        return $dispatcher;
    }

    public function testGetDefaultTwig()
    {
        $extension = new TwigExtension();
        $this->assertInstanceOf(\Twig_Environment::class, $extension['twig']);

        return $extension['twig'];
    }

    public function testGetConfiguredTwig()
    {
        $extension = new TwigExtension([
            'twig_cache' => sys_get_temp_dir(),
            'twig_root' => __DIR__,
        ]);
        $this->assertInstanceOf(\Twig_Environment::class, $extension['twig']);

        return $extension['twig'];
    }

    /**
     * @depends testGetDefaultTwig
     */
    public function testDefaultTwigCache(\Twig_Environment $default)
    {
        $this->assertFalse($default->getCache());
    }

    /**
     * @depends testGetConfiguredTwig
     */
    public function testConfigurableTwigCache(\Twig_Environment $configured)
    {
        $this->assertEquals(sys_get_temp_dir(), $configured->getCache());
    }

    /**
     * @depends testGetDefaultTwig
     */
    public function testDefaultTwigRoot(\Twig_Environment $default)
    {
        $dir = getcwd();
        $this->assertEquals(new \Twig_Loader_Filesystem([\Twig_Loader_Filesystem::MAIN_NAMESPACE => $dir], $dir), $default->getLoader());
    }

    /**
     * @depends testGetConfiguredTwig
     */
    public function testConfigurableTwigRoot(\Twig_Environment $configured)
    {
        $dir = __DIR__;
        $this->assertEquals(new \Twig_Loader_Filesystem([\Twig_Loader_Filesystem::MAIN_NAMESPACE => $dir], $dir), $configured->getLoader());
    }
}
