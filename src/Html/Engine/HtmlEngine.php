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
use LastCall\Mannequin\Core\Exception\UnsupportedComponentException;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Html\Component\HtmlComponent;

class HtmlEngine implements EngineInterface
{
    public function __construct()
    {
    }

    public function render(ComponentInterface $component, array $values = [], Rendered $rendered)
    {
        if ($this->supports($component)) {
            $rendered->setMarkup(
                file_get_contents($component->getFile()->getPathname())
            );

            return;
        }
        throw new UnsupportedComponentException('Unsupported component.');
    }

    public function supports(ComponentInterface $component): bool
    {
        return $component instanceof HtmlComponent;
    }

    public function renderSource(ComponentInterface $component): string
    {
        if ($this->supports($component)) {
            return file_get_contents($component->getFile()->getPathname());
        }
        throw new UnsupportedComponentException('Unsupported component.');
    }
}
