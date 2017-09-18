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

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Html\Component\HtmlComponent;

class HtmlEngine implements EngineInterface
{
    public function __construct()
    {
    }

    public function render(ComponentInterface $pattern, array $values = [], Rendered $rendered)
    {
        if ($this->supports($pattern)) {
            $rendered->setMarkup(
                file_get_contents($pattern->getFile()->getPathname())
            );

            return;
        }
        throw new UnsupportedPatternException('Unsupported Pattern.');
    }

    public function supports(ComponentInterface $pattern): bool
    {
        return $pattern instanceof HtmlComponent;
    }

    public function renderSource(ComponentInterface $pattern): string
    {
        if ($this->supports($pattern)) {
            return file_get_contents($pattern->getFile()->getPathname());
        }
        throw new UnsupportedPatternException('Unsupported pattern.');
    }
}
