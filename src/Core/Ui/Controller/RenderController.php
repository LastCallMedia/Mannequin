<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RenderController
{
    private $collection;

    private $engine;

    private $ui;

    public function __construct(
        PatternCollection $collection,
        EngineInterface $engine,
        UiInterface $ui
    ) {
        $this->collection = $collection;
        $this->engine = $engine;
        $this->ui = $ui;
    }

    public function renderAction($pattern, $set)
    {
        $rendered = $this->renderPattern($pattern, $set);

        return new Response($this->ui->decorateRendered($rendered));
    }

    private function renderPattern($pattern, $set)
    {
        if ($pattern = $this->collection->get($pattern)) {
            if ($set = $pattern->getVariableSets()[$set]) {
                return $this->engine->render($pattern, $set);
            }
        }
        throw new NotFoundHttpException('Pattern not found.');
    }

    public function renderRawAction($pattern, $set)
    {
        $rendered = $this->renderPattern($pattern, $set);

        return new Response($rendered->getMarkup());
    }

    public function renderSourceAction($pattern)
    {
        if ($pattern = $this->collection->get($pattern)) {
            $markup = $this->engine->renderSource($pattern);

            return new Response($markup);
        }
    }
}
