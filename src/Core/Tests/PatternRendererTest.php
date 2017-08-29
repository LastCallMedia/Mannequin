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

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\PatternRenderer;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Stubs\TestFilePattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PatternRendererTest extends TestCase
{
    public function testDispatchesEvents()
    {
        $eventProphecy = Argument::that(function ($arg) {
            return $arg instanceof RenderEvent && $arg->isRoot();
        });
        $pattern = new TestFilePattern('foo', [], new \SplFileInfo(__FILE__));
        $variant = $pattern->createVariant('foo', 'Foo');

        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher
            ->dispatch(PatternEvents::PRE_RENDER, $eventProphecy)
            ->shouldBeCalled();
        $dispatcher
            ->dispatch(PatternEvents::POST_RENDER, $eventProphecy)
            ->shouldBeCalled();

        $engine = $this->prophesize(EngineInterface::class);
        $engine
            ->render($pattern, [], Argument::type(Rendered::class))
            ->shouldBeCalled();
        $renderer = new PatternRenderer($engine->reveal(), $dispatcher->reveal());

        $renderer->render(new PatternCollection(), $pattern, $variant);
    }

    public function testSetsIsRoot()
    {
        $collection = new PatternCollection();
        $pattern = new TestFilePattern('foo', [], new \SplFileInfo(__FILE__));
        $variant = $pattern->createVariant('foo', 'Foo');

        $firstEventProphecy = Argument::that(function ($arg) use (&$renderer, $pattern, $variant) {
            return $arg instanceof RenderEvent && $arg->isRoot();
        });
        $secondEventProphecy = Argument::that(function ($arg) use (&$renderer, $pattern, $variant) {
            return $arg instanceof RenderEvent && !$arg->isRoot();
        });
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher
            ->dispatch(PatternEvents::PRE_RENDER, $firstEventProphecy)
            ->will(function () use (&$renderer, $collection, $pattern, $variant) {
                $renderer->render($collection, $pattern, $variant);
            })
            ->shouldBeCalledTimes(1);
        $dispatcher
            ->dispatch(PatternEvents::PRE_RENDER, $secondEventProphecy)
            ->shouldBeCalledTimes(1);

        $dispatcher
            ->dispatch(PatternEvents::POST_RENDER, $firstEventProphecy)
            ->shouldBeCalledTimes(1);
        $dispatcher
            ->dispatch(PatternEvents::POST_RENDER, $secondEventProphecy)
            ->shouldBeCalledTimes(1);

        $engine = $this->prophesize(EngineInterface::class);
        $engine
            ->render($pattern, [], Argument::type(Rendered::class))
            ->shouldBeCalled();
        $renderer = new PatternRenderer($engine->reveal(), $dispatcher->reveal());
        $renderer->render($collection, $pattern, $variant);
    }
}
