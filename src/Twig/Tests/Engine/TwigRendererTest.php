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

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Engine\RendererTestCase;
use LastCall\Mannequin\Twig\Driver\PreloadedTwigDriver;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigRendererTest extends RendererTestCase
{
    public function getRenderer(): EngineInterface
    {
        $driver = $this->getDriver();

        return new TwigEngine($driver, ['foo'], ['bar']);
    }

    private function getDriver()
    {
        $loader = new \Twig_Loader_Array([
            'test' => 'This is {{"html"}}',
        ]);
        $twig = new \Twig_Environment($loader);

        return new PreloadedTwigDriver($twig);
    }

    public function testWrapsRendered()
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $twig->render('test', ['foo' => new \Twig_Markup('bar', 'UTF-8')])
            ->willReturn('rendered')
            ->shouldBeCalled();

        $driver = new PreloadedTwigDriver($twig->reveal());

        $pattern = $this->getSupportedPattern();
        $renderer = new TwigEngine($driver, ['foostyle'], ['fooscript']);
        $rendered = new Rendered(['@pattern_css'], ['@pattern_js']);
        $rendered->setMarkup('bar');

        $output = new Rendered();
        $renderer->render($pattern, ['foo' => $rendered], $output);
    }

    public function getSupportedPattern(): PatternInterface
    {
        $driver = $this->getDriver();
        $template = $driver->getTwig()->load('test');

        return new TwigPattern('supported', [], $template->getSourceContext());
    }
}
