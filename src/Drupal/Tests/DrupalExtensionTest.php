<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Drupal\Driver\DrupalTwigDriver;
use LastCall\Mannequin\Drupal\DrupalExtension;
use LastCall\Mannequin\Drupal\Subscriber\DefaultVariablesSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use LastCall\Mannequin\Drupal\Drupal\MannequinExtensionDiscovery;
use Twig\Cache\FilesystemCache;

class DrupalExtensionTest extends ExtensionTestCase
{
    use UsesTestDrupalRoot;

    public static function setUpBeforeClass()
    {
        self::requireDrupalClasses();
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(Argument::type(DefaultVariablesSubscriber::class))
            ->shouldBeCalled();
        $dispatcher->addSubscriber(Argument::type(EventSubscriberInterface::class))
            ->shouldBeCalled();

        return $dispatcher;
    }

    public function testGetFunctions()
    {
        $functions = parent::testGetFunctions();
        $names = array_map(function ($fn) {
            return $fn->getName();
        }, $functions);
        $this->assertEquals([
            'attributes',
        ], $names);
    }

    public function getExtension(): ExtensionInterface
    {
        return new DrupalExtension([
            'drupal_root' => $this->getDrupalRoot(),
        ]);
    }

    public function testDriverGetsNamespaces()
    {
        $extension = new ExposedDrupalExtension(['drupal_root' => self::getDrupalRoot()]);
        $extension->addTwigPath('foo', '../Resources');
        $mannequin = $this->getMannequin();

        $discovery = new MannequinExtensionDiscovery(self::getDrupalRoot(), $mannequin->getCache());
        $expected = new DrupalTwigDriver(self::getDrupalRoot(), $discovery, [], [
            'foo' => ['../Resources'],
        ], ['stable']);
        $expected->setCache(new FilesystemCache(sys_get_temp_dir().'/mannequin-test/twig'));
        $extension->register($mannequin);
        $this->assertEquals(
            $expected,
            $extension->getTwigDriver()
        );
    }

    public function testDriverGetsFallbackExtensions()
    {
        $extension = new ExposedDrupalExtension(['drupal_root' => self::getDrupalRoot()]);
        $extension->setFallbackExtensions(['classy']);
        $mannequin = $this->getMannequin();
        $extension->register($mannequin);

        $discovery = new MannequinExtensionDiscovery(self::getDrupalRoot(), $mannequin->getCache());
        $expected = new DrupalTwigDriver(self::getDrupalRoot(), $discovery, [], [], ['classy']);
        $expected->setCache(new FilesystemCache(sys_get_temp_dir().'/mannequin-test/twig'));

        $this->assertEquals(
            $expected,
            $extension->getTwigDriver()
        );
    }
}

class ExposedDrupalExtension extends DrupalExtension
{
    public function getTwigDriver()
    {
        return $this->getDriver();
    }
}
