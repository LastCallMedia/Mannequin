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

use Assetic\AssetWriter;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\PatternVariant;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PatternRenderer
{
    private $engine;
    private $dispatcher;

    public function __construct(EngineInterface $engine, EventDispatcherInterface $dispatcher)
    {
        $this->engine = $engine;
        $this->dispatcher = $dispatcher;
    }

    public function render(PatternCollection $collection, PatternInterface $pattern, PatternVariant $variant): Rendered
    {
        $rendered = new Rendered();
        $rendered->setCss(['@global_css']);
        $rendered->setJs(['@global_js']);
        $event = new RenderEvent($collection, $pattern, $variant, $rendered);
        $this->dispatcher->dispatch(PatternEvents::PRE_RENDER, $event);
        $this->engine->render($pattern, $event->getVariables(), $rendered);
        $this->dispatcher->dispatch(PatternEvents::POST_RENDER, $event);

        return $rendered;
    }

    public function renderSource(PatternInterface $pattern): string
    {
        return $this->engine->renderSource($pattern);
    }

    public function writeAssets(Rendered $rendered, string $assetDirectory)
    {
        $writer = new AssetWriter($assetDirectory);
        foreach ($rendered->getAssets()->all() as $asset) {
            $writer->writeAsset($asset);
        }
    }
}
