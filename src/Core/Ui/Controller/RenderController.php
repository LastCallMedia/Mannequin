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

use LastCall\Mannequin\Core\Asset\AssetManager;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Exception\PatternNotFoundException;
use LastCall\Mannequin\Core\Exception\VariantNotFoundException;
use LastCall\Mannequin\Core\PatternRenderer;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RenderController
{
    private $collection;

    private $renderer;

    private $ui;

    private $assetDir;

    private $assetManager;

    public function __construct(
        ComponentCollection $collection,
        PatternRenderer $renderer,
        UiInterface $ui,
        AssetManager $assetManager,
        string $assetDir
    ) {
        $this->collection = $collection;
        $this->renderer = $renderer;
        $this->ui = $ui;
        $this->assetManager = $assetManager;
        $this->assetDir = $assetDir;
    }

    public function renderAction($pattern, $variant)
    {
        $rendered = $this->renderPattern($pattern, $variant);
        $this->assetManager->write($this->assetDir);

        return new Response($this->ui->decorateRendered(
            $rendered
        ));
    }

    private function renderPattern($pattern, $variant)
    {
        $pattern = $this->getPattern($pattern);
        $variant = $this->getPatternVariant($pattern, $variant);

        return $this->renderer->render($this->collection, $pattern, $variant);
    }

    public function renderRawAction($pattern, $variant)
    {
        $rendered = $this->renderPattern($pattern, $variant);

        return new Response($rendered->getMarkup());
    }

    public function renderSourceAction($pattern)
    {
        $pattern = $this->getPattern($pattern);

        return new Response($this->renderer->renderSource($pattern));
    }

    private function getPattern($patternId)
    {
        try {
            return $this->collection->get($patternId);
        } catch (PatternNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }

    private function getPatternVariant(ComponentInterface $pattern, $variantId)
    {
        try {
            return $pattern->getVariant($variantId);
        } catch (VariantNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }
}
