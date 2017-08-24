<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Driver;

interface TwigDriverInterface
{
    public function getTwig(): \Twig_Environment;

    public function getNamespaces(): array;

    public function getTwigRoot(): string;
}
