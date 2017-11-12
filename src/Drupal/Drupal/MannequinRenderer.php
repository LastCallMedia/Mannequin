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

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\RenderableInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;

/**
 * Stub class to stand in for Drupal's standard Renderer.
 *
 * This renderer simply concatenates array values. For example, given an array
 * `content`, when `content` is passed to render, all the values will be
 * stringified and concatenated. This allows us to mock Drupal's render system
 * in Mannequin.
 */
class MannequinRenderer implements RendererInterface
{
    public function render(&$elements, $is_root_call = false)
    {
        if (is_scalar($elements)) {
            return Html::escape($elements);
        }
        if (is_object($elements)) {
            if ($elements instanceof RenderableInterface) {
                // @todo: Can you actually pass a renderable object into render?
                return $this->render($elements->toRenderable());
            }
            if (method_exists($elements, '__toString')) {
                return $elements->__toString();
            }
            if (method_exists($elements, 'toString')) {
                return $elements->toString();
            }
        }
        if (is_array($elements)) {
            if (isset($elements['#type'])) {
                // @todo: Call render{$type} if we know how to render an element type.
                throw new \InvalidArgumentException('Unable to render %s elements.', $elements['#type']);
            }
            $parts = array_map([$this, 'render'], $this->renderableChildren($elements));

            return implode('', $parts);
        }

        throw new \InvalidArgumentException(sprintf('Unable to render variable of type: %s', gettype()));
    }

    private function renderableChildren(array $elements)
    {
        return array_filter($elements, function ($key) {
            return 0 !== strpos($key, '#');
        }, ARRAY_FILTER_USE_KEY);
    }

    public function renderRoot(&$elements)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function renderPlain(&$elements)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function renderPlaceholder($placeholder, array $elements)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function hasRenderContext()
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function executeInRenderContext(
        RenderContext $context,
        callable $callable
    ) {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function mergeBubbleableMetadata(array $a, array $b)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function addCacheableDependency(array &$elements, $dependency)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }
}
