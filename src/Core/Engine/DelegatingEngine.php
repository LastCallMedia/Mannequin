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

use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Variable\Set;

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

    public function supports(PatternInterface $pattern): bool
    {
        return (bool) $this->findRendererFor($pattern, false);
    }

    private function findRendererFor(PatternInterface $pattern, $require = true)
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
    }

    public function render(PatternInterface $pattern, Set $set): Rendered
    {
        return $this->findRendererFor($pattern)->render($pattern, $set);
    }

    public function renderSource(PatternInterface $pattern): string
    {
        return $this->findRendererFor($pattern)->renderSource($pattern);
    }
}