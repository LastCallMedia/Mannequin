<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PatternRenderer
{
    private $engine;
    private $dispatcher;
    private $isRendering = false;

    public function __construct(EngineInterface $engine, EventDispatcherInterface $dispatcher)
    {
        $this->engine = $engine;
        $this->dispatcher = $dispatcher;
    }

    public function render(ComponentCollection $collection, ComponentInterface $pattern, Sample $variant): Rendered
    {
        return $this->enterRender(function ($isRoot) use ($collection, $pattern, $variant) {
            $rendered = new Rendered();
            $event = new RenderEvent($collection, $pattern, $variant, $rendered, $isRoot);
            $this->dispatcher->dispatch(PatternEvents::PRE_RENDER, $event);
            $this->engine->render($pattern, $event->getVariables(), $rendered);
            $this->dispatcher->dispatch(PatternEvents::POST_RENDER, $event);

            return $rendered;
        });
    }

    private function enterRender(callable $cb)
    {
        $isRoot = !$this->isRendering;
        $this->isRendering = true;
        $return = $cb($isRoot);
        $this->isRendering = !$isRoot;

        return $return;
    }

    public function renderSource(ComponentInterface $pattern): string
    {
        return $this->engine->renderSource($pattern);
    }
}
