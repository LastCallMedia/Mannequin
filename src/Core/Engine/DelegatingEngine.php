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
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
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

    public function supports(ComponentInterface $pattern): bool
    {
        return (bool) $this->findRendererFor($pattern, false);
    }

    private function findRendererFor(ComponentInterface $pattern, $require = true)
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($pattern)) {
                return $renderer;
            }
        }
        if ($require) {
            throw new UnsupportedPatternException(
                sprintf('Unable to find a renderer for %s', get_class($pattern))
            );
        }

        return false;
    }

    public function render(ComponentInterface $pattern, array $variables = [], Rendered $rendered)
    {
        return $this->findRendererFor($pattern)->render($pattern, $variables, $rendered);
    }

    public function renderSource(ComponentInterface $pattern): string
    {
        return $this->findRendererFor($pattern)->renderSource($pattern);
    }
}
