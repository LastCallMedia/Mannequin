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
use LastCall\Mannequin\Core\Exception\UnsupportedComponentException;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Twig\Component\TwigComponent;

class TwigEngine implements EngineInterface
{
    public function render(ComponentInterface $component, array $variables = [], Rendered $rendered)
    {
        if ($this->supports($component)) {
            $twig = $component->getTwig();
            $rendered->setMarkup(
                $twig->render(
                    $component->getSource()->getName(),
                    $this->wrapRendered($variables)
                )
            );

            return;
        }
        throw new UnsupportedComponentException(sprintf('Unsupported component: %s', $component->getId()));
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

    public function supports(ComponentInterface $component): bool
    {
        return $component instanceof TwigComponent;
    }

    public function renderSource(ComponentInterface $component): string
    {
        /** @var TwigComponent $component */
        if ($this->supports($component)) {
            return twig_source($component->getTwig(), $component->getSource()->getName());
        }
        throw new UnsupportedComponentException('Unsupported component.');
    }
}
