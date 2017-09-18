<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Engine;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Exception\UnsupportedComponentException;
use LastCall\Mannequin\Core\Rendered;

class DelegatingEngine implements EngineInterface
{
    private $renderers = [];

    public function __construct(array $renderers = [])
    {
        foreach ($renderers as $renderer) {
            if (!$renderer instanceof EngineInterface) {
                throw new \InvalidArgumentException(
                    'Renderer must implement EngineInterface.'
                );
            }
        }
        $this->renderers = $renderers;
    }

    public function supports(ComponentInterface $component): bool
    {
        return (bool) $this->findRendererFor($component, false);
    }

    private function findRendererFor(ComponentInterface $component, $require = true)
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($component)) {
                return $renderer;
            }
        }
        if ($require) {
            throw new UnsupportedComponentException(
                sprintf('Unable to find a renderer for %s', get_class($component))
            );
        }

        return false;
    }

    public function render(ComponentInterface $component, array $variables = [], Rendered $rendered)
    {
        return $this->findRendererFor($component)->render($component, $variables, $rendered);
    }

    public function renderSource(ComponentInterface $component): string
    {
        return $this->findRendererFor($component)->renderSource($component);
    }
}
