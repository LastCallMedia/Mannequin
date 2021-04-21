<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Component;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Tests\Component\ComponentTestCase;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use Twig\Environment;
use Twig\Source;


class TwigComponentTest extends ComponentTestCase
{
    public function getComponent(): ComponentInterface
    {
        $twig = $this->prophesize(Environment::class);
        $src = new Source('', 'test', self::TEMPLATE_FILE);

        return new TwigComponent(self::COMPONENT_ID, self::COMPONENT_ALIASES, $src, $twig->reveal());
    }

    public function testRawFormat()
    {
        $component = $this->getComponent();
        $this->assertArraySubset([
            'source_format' => 'twig',
        ], $component->getMetadata());
    }
}
