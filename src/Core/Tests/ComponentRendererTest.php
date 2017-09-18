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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\ComponentRenderer;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Stubs\TestFileComponent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ComponentRendererTest extends TestCase
{
    public function testDispatchesEvents()
    {
        $eventProphecy = Argument::that(function ($arg) {
            return $arg instanceof RenderEvent && $arg->isRoot();
        });
        $component = new TestFileComponent('foo', [], new \SplFileInfo(__FILE__));
        $variant = $component->createVariant('foo', 'Foo');

        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher
            ->dispatch(ComponentEvents::PRE_RENDER, $eventProphecy)
            ->shouldBeCalled();
        $dispatcher
            ->dispatch(ComponentEvents::POST_RENDER, $eventProphecy)
            ->shouldBeCalled();

        $engine = $this->prophesize(EngineInterface::class);
        $engine
            ->render($component, [], Argument::type(Rendered::class))
            ->shouldBeCalled();
        $renderer = new ComponentRenderer($engine->reveal(), $dispatcher->reveal());

        $renderer->render(new ComponentCollection(), $component, $variant);
    }

    public function testSetsIsRoot()
    {
        $collection = new ComponentCollection();
        $component = new TestFileComponent('foo', [], new \SplFileInfo(__FILE__));
        $variant = $component->createVariant('foo', 'Foo');

        $firstEventProphecy = Argument::that(function ($arg) use (&$renderer, $component, $variant) {
            return $arg instanceof RenderEvent && $arg->isRoot();
        });
        $secondEventProphecy = Argument::that(function ($arg) use (&$renderer, $component, $variant) {
            return $arg instanceof RenderEvent && !$arg->isRoot();
        });
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher
            ->dispatch(ComponentEvents::PRE_RENDER, $firstEventProphecy)
            ->will(function () use (&$renderer, $collection, $component, $variant) {
                $renderer->render($collection, $component, $variant);
            })
            ->shouldBeCalledTimes(1);
        $dispatcher
            ->dispatch(ComponentEvents::PRE_RENDER, $secondEventProphecy)
            ->shouldBeCalledTimes(1);

        $dispatcher
            ->dispatch(ComponentEvents::POST_RENDER, $firstEventProphecy)
            ->shouldBeCalledTimes(1);
        $dispatcher
            ->dispatch(ComponentEvents::POST_RENDER, $secondEventProphecy)
            ->shouldBeCalledTimes(1);

        $engine = $this->prophesize(EngineInterface::class);
        $engine
            ->render($component, [], Argument::type(Rendered::class))
            ->shouldBeCalled();
        $renderer = new ComponentRenderer($engine->reveal(), $dispatcher->reveal());
        $renderer->render($collection, $component, $variant);
    }
}
