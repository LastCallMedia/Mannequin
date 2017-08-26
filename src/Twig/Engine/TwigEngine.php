<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Engine;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigEngine implements EngineInterface
{
    private $driver;

    public function __construct(
        TwigDriverInterface $driver
    ) {
        $this->driver = $driver;
    }

    public function render(PatternInterface $pattern, array $variables = [], Rendered $rendered)
    {
        if ($this->supports($pattern)) {
            $rendered->setMarkup(
                $this->driver->getTwig()->render(
                    $pattern->getSource()->getName(),
                    $this->wrapRendered($variables)
                )
            );

            return;
        }
        throw new UnsupportedPatternException(sprintf('Unsupported pattern: %s', $pattern->getId()));
    }

    private function wrapRendered(array $variables)
    {
        $wrapped = [];
        foreach ($variables as $key => $value) {
            if ($value instanceof Rendered) {
                $wrapped[$key] = new \Twig_Markup($value, 'UTF-8');
            } else {
                $wrapped[$key] = is_array($value)
                    ? $this->wrapRendered($value)
                    : $value;
            }
        }

        return $wrapped;
    }

    public function supports(PatternInterface $pattern): bool
    {
        return $pattern instanceof TwigPattern;
    }

    public function renderSource(PatternInterface $pattern): string
    {
        if ($this->supports($pattern)) {
            return $pattern->getSource()->getCode();
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
    }
}
