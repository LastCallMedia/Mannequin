<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Drupal;

use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;

/**
 * Stub class to stand in for Drupal's standard Renderer.
 */
class MannequinRenderer implements RendererInterface
{
    public function renderRoot(&$elements)
    {
        throw new \Exception('Method not yet implemented');
    }

    public function renderPlain(&$elements)
    {
        throw new \Exception('Method not yet implemented');
    }

    public function renderPlaceholder($placeholder, array $elements)
    {
        throw new \Exception('Method not yet implemented');
    }

    public function render(&$elements, $is_root_call = false)
    {
        throw new \Exception('Method not yet implemented');
    }

    public function hasRenderContext()
    {
        throw new \Exception('Method not yet implemented');
    }

    public function executeInRenderContext(
        RenderContext $context,
        callable $callable
    ) {
        throw new \Exception('Method not yet implemented');
    }

    public function mergeBubbleableMetadata(array $a, array $b)
    {
        throw new \Exception('Method not yet implemented');
    }

    public function addCacheableDependency(array &$elements, $dependency)
    {
        throw new \Exception('Method not yet implemented');
    }
}
