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
use LastCall\Mannequin\Core\Exception\UnknownComponentException;
use LastCall\Mannequin\Core\Exception\VariantNotFoundException;
use LastCall\Mannequin\Core\ComponentRenderer;
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
        ComponentRenderer $renderer,
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

    public function renderAction($component, $sample)
    {
        $rendered = $this->renderComponent($component, $sample);
        $this->assetManager->write($this->assetDir);

        return new Response($this->ui->decorateRendered(
            $rendered
        ));
    }

    private function renderComponent($component, $sample)
    {
        $component = $this->getComponent($component);
        $sample = $this->getComponentSample($component, $sample);

        return $this->renderer->render($this->collection, $component, $sample);
    }

    public function renderRawAction($component, $sample)
    {
        $rendered = $this->renderComponent($component, $sample);

        return new Response($rendered->getMarkup());
    }

    public function renderSourceAction($component)
    {
        $component = $this->getComponent($component);

        return new Response($this->renderer->renderSource($component));
    }

    private function getComponent($componentId)
    {
        try {
            return $this->collection->get($componentId);
        } catch (UnknownComponentException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }

    private function getComponentSample(ComponentInterface $component, $sampleId)
    {
        try {
            return $component->getVariant($sampleId);
        } catch (VariantNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }
}
