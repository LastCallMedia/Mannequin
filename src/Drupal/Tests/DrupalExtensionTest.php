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
use LastCall\Mannequin\Drupal\DrupalExtension;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DrupalExtensionTest extends ExtensionTestCase
{
    use UsesTestDrupalRoot;

    public static function setUpBeforeClass()
    {
        self::requireDrupalClasses();
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        // For right now, we don't really test subscribers, because the Drupal
        // extension just uses the TwigExtension's subscribers.
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
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
}
