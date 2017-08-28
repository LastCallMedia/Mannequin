<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Pattern;

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Tests\Pattern\PatternTestCase;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigPatternTest extends PatternTestCase
{
    public function getPattern(): PatternInterface
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $src = new \Twig_Source('', 'test', self::TEMPLATE_FILE);

        return new TwigPattern(self::PATTERN_ID, self::PATTERN_ALIASES, $src, $twig->reveal());
    }

    public function testRawFormat()
    {
        $pattern = $this->getPattern();
        $this->assertArraySubset([
            'source_format' => 'twig',
        ], $pattern->getMetadata());
    }
}
