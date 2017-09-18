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

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Twig\Component\TwigComponent;

class TwigEngine implements EngineInterface
{
    public function render(ComponentInterface $pattern, array $variables = [], Rendered $rendered)
    {
        if ($this->supports($pattern)) {
            $twig = $pattern->getTwig();
            $rendered->setMarkup(
                $twig->render(
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

    public function supports(ComponentInterface $pattern): bool
    {
        return $pattern instanceof TwigComponent;
    }

    public function renderSource(ComponentInterface $pattern): string
    {
        /** @var TwigComponent $pattern */
        if ($this->supports($pattern)) {
            return twig_source($pattern->getTwig(), $pattern->getSource()->getName());
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
    }
}
