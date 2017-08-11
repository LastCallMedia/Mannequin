<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Engine;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;

class HtmlEngine implements EngineInterface
{
    public function __construct()
    {
    }

    public function render(PatternInterface $pattern, array $values = []): Rendered
    {
        if ($this->supports($pattern)) {
            $rendered = new Rendered();
            $rendered->setMarkup(
                file_get_contents($pattern->getFile()->getPathname())
            );
            $rendered->setCss(['@global_css']);
            $rendered->setJs(['@global_js']);

            return $rendered;
        }
        throw new UnsupportedPatternException('Unsupported Pattern.');
    }

    public function supports(PatternInterface $pattern): bool
    {
        return $pattern instanceof HtmlPattern;
    }

    public function renderSource(PatternInterface $pattern): string
    {
        if ($this->supports($pattern)) {
            return file_get_contents($pattern->getFile()->getPathname());
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
    }
}
