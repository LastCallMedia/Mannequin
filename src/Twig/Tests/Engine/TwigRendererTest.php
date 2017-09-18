<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Engine;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Engine\RendererTestCase;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Component\TwigComponent;

class TwigRendererTest extends RendererTestCase
{
    public function getRenderer(): EngineInterface
    {
        return new TwigEngine();
    }

    public function testWrapsRendered()
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $twig->render('wrapped', ['foo' => new \Twig_Markup('bar', 'UTF-8')])
            ->willReturn('rendered')
            ->shouldBeCalled();

        $source = new \Twig_Source('', 'wrapped', '');
        $component = new TwigComponent('wrapping', [], $source, $twig->reveal());

        $engine = new TwigEngine();
        $rendered = new Rendered();
        $rendered->setMarkup('bar');

        $output = new Rendered();
        $engine->render($component, ['foo' => $rendered], $output);
    }

    public function getSupportedComponent(): ComponentInterface
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array([
            'test' => 'This is {{"html"}}',
        ]));
        $source = $twig->load('test')->getSourceContext();

        return new TwigComponent('supported', [], $source, $twig);
    }
}
