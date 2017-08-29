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

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;

interface EngineInterface
{
    public function supports(PatternInterface $pattern): bool;

    public function render(PatternInterface $pattern, array $values = [], Rendered $rendered);

    public function renderSource(PatternInterface $pattern): string;
}
