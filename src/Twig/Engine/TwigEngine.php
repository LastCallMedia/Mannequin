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
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigEngine implements EngineInterface
{
    private $twig;

    private $styles = [];

    private $scripts = [];

    public function __construct(
        \Twig_Environment $twig,
        SetResolver $setResolver,
        array $styles = [],
        array $scripts = []
    ) {
        $this->twig = $twig;
        $this->styles = $styles;
        $this->scripts = $scripts;
        $this->setResolver = $setResolver;
    }

    public function render(PatternInterface $pattern, Set $set): Rendered
    {
        if ($this->supports($pattern)) {
            $styles = $this->styles;
            $scripts = $this->scripts;
            $rendered = new Rendered();
            $variables = $this->setResolver->resolveSet(
                $pattern->getVariableDefinition(),
                $set
            );
            foreach ($variables as &$variable) {
                if ($variable instanceof Rendered) {
                    $styles = array_merge($styles, $variable->getStyles());
                    $scripts = array_merge($scripts, $variable->getScripts());
                    $variable = new \Twig_Markup($variable, 'UTF-8');
                }
            }
            $rendered->setMarkup(
                $this->twig->render(
                    $pattern->getSource()->getName(),
                    $variables
                )
            );
            $rendered->setStyles($this->styles);
            $rendered->setScripts($this->scripts);

            return $rendered;
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
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
