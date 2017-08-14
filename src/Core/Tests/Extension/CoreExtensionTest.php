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

use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Subscriber\CssJsRenderSubscriber;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\VariableResolverSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CoreExtensionTest extends ExtensionTestCase
{
    public function getExtension(): ExtensionInterface
    {
        return new CoreExtension();
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(YamlFileMetadataSubscriber::class)
        )->shouldBeCalled();
        $dispatcher->addSubscriber(
            Argument::type(LastChanceNameSubscriber::class)
        )->shouldBeCalled();

        $dispatcher->addSubscriber(
            Argument::type(CssJsRenderSubscriber::class)
        )->shouldBeCalled();

        $dispatcher->addSubscriber(
            Argument::type(VariableResolverSubscriber::class)
        )->shouldBeCalled();

        return $dispatcher;
    }
}
