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
use LastCall\Mannequin\Core\Exception\PatternNotFoundException;
use LastCall\Mannequin\Core\Exception\VariantNotFoundException;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Ui\UiInterface;
use LastCall\Mannequin\Core\Variable\VariableResolver;
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
        UiInterface $ui,
        VariableResolver $resolver
    ) {
        $this->collection = $collection;
        $this->engine = $engine;
        $this->ui = $ui;
        $this->resolver = $resolver;
    }

    public function renderAction($pattern, $variant)
    {
        $rendered = $this->renderPattern($pattern, $variant);

        return new Response($this->ui->decorateRendered($rendered));
    }

    private function renderPattern($pattern, $variant)
    {
        $pattern = $this->getPattern($pattern);
        $variant = $this->getPatternVariant($pattern, $variant);
        $resolved = $this->resolver->resolve($variant->getVariables(), [
            'collection' => $this->collection,
            'resolver' => $this->resolver,
            'engine' => $this->engine,
            'pattern' => $pattern,
            'variant' => $variant,
        ]);

        return $this->engine->render($pattern, $resolved);
    }

    public function renderRawAction($pattern, $variant)
    {
        $rendered = $this->renderPattern($pattern, $variant);

        return new Response($rendered->getMarkup());
    }

    public function renderSourceAction($pattern)
    {
        $pattern = $this->getPattern($pattern);
        $markup = $this->engine->renderSource($pattern);

        return new Response($markup);
    }

    private function getPattern($patternId)
    {
        try {
            return $this->collection->get($patternId);
        } catch (PatternNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }

    private function getPatternVariant(PatternInterface $pattern, $variantId)
    {
        try {
            return $pattern->getVariant($variantId);
        } catch (VariantNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }
}
