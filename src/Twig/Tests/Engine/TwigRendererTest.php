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
use LastCall\Mannequin\Core\Tests\Engine\RendererTestCase;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigRendererTest extends RendererTestCase
{
    public function getRenderer(): EngineInterface
    {
        return new TwigEngine(
            $this->getTwig(),
            new SetResolver(),
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

    public function testResolvesVariables()
    {
        $twig = $this->prophesize(\Twig_Environment::class);
        $twig->render('form-input.twig', ['foo' => 'bar - resolved'])
            ->willReturn('rendered');

        $pattern = $this->getSupportedPattern();
        $setResolver = $this->prophesize(SetResolver::class);
        $setResolver->resolveSet(
            $pattern->getVariableDefinition(),
            $pattern->getVariableSets()['default']
        )
            ->shouldBeCalled()
            ->willReturn(['foo' => 'bar - resolved']);

        $renderer = new TwigEngine($twig->reveal(), $setResolver->reveal());
        $renderer->render($pattern, $pattern->getVariableSets()['default']);
    }

    public function getSupportedPattern(): PatternInterface
    {
        $src = new \Twig_Source('', 'form-input.twig', 'form-input.twig');

        return new TwigPattern('supported', [], $src);
    }
}
