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
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigRendererTest extends RendererTestCase
{
    public function getRenderer(): EngineInterface
    {
        return new TwigEngine(
            $this->getTwig(),
            ['foo'],
            ['bar']
        );
    }

    private function getTwig()
    {
        $loader = new \Twig_Loader_Filesystem([__DIR__.'/../Resources/']);
        $twig = new \Twig_Environment($loader);

        return $twig;
    }

    public function testRender()
    {
        $rendered = parent::testRender();
        $this->assertEquals(['foo'], $rendered->getStyles());
        $this->assertEquals(['bar'], $rendered->getScripts());
    }

    public function testWrapsRendered()
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $twig->render('form-input.twig', ['foo' => new \Twig_Markup('bar', 'UTF-8')])
            ->willReturn('rendered')
            ->shouldBeCalled();

        $pattern = $this->getSupportedPattern();
        $renderer = new TwigEngine($twig->reveal(), ['foostyle'], ['fooscript']);
        $rendered = new Rendered(['barstyle'], ['barscript']);
        $rendered->setMarkup('bar');

        return $renderer->render($pattern, ['foo' => $rendered]);
    }

    /**
     * @depends testWrapsRendered
     */
    public function testAggregatesStyles(Rendered $rendered)
    {
        $this->assertEquals(['foostyle', 'barstyle'], $rendered->getStyles());
    }

    /**
     * @depends testWrapsRendered
     */
    public function testAggregatesScripts(Rendered $rendered)
    {
        $this->assertEquals(['fooscript', 'barscript'], $rendered->getScripts());
    }

    public function getSupportedPattern(): PatternInterface
    {
        $src = new \Twig_Source('', 'form-input.twig', 'form-input.twig');

        return new TwigPattern('supported', [], $src);
    }
}
