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

use LastCall\Mannequin\Core\Component\BrokenComponent;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\TemplateFileInterface;
use LastCall\Mannequin\Core\Rendered;

class BrokenEngine implements EngineInterface
{
    public function render(ComponentInterface $component, array $values = [], Rendered $rendered)
    {
        $rendered->setMarkup('<h2>This component cannot be rendered.</h2>');
    }

    public function renderSource(ComponentInterface $component): string
    {
        if ($component instanceof TemplateFileInterface) {
            if (false !== $file = $component->getFile()) {
                return file_get_contents($file);
            }
        }

        return '';
    }

    public function supports(ComponentInterface $component): bool
    {
        return $component instanceof BrokenComponent;
    }
}
