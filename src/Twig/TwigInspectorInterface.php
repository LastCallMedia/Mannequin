<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig;

interface TwigInspectorInterface
{
    public function inspectLinked(\Twig_Source $source): array;

    /**
     * @param \Twig_Source $source
     *
     * @return string|false
     */
    public function inspectPatternData(\Twig_Source $source);
}
