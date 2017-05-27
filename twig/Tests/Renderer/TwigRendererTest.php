<?php


namespace LastCall\Mannequin\Twig\Tests\Renderer;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Tests\Render\RendererTestCase;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Render\TwigRenderer;

class TwigRendererTest extends RendererTestCase {

  public function getRenderer(): RendererInterface {
    return new TwigRenderer($this->getTwig(), new SetResolver(), ['foo'], ['bar']);
  }

  public function getSupportedPattern(): PatternInterface {
    $src = new \Twig_Source('', 'twig-no-metadata.twig', 'twig-no-metadata.twig');
    return new TwigPattern('supported', [], $src);
  }

  private function getTwig() {
    $loader = new \Twig_Loader_Filesystem([__DIR__.'/../Resources/']);
    $twig = new \Twig_Environment($loader);
    return $twig;
  }

  public function testRender() {
    $rendered = parent::testRender();
    $this->assertEquals(['foo'], $rendered->getStyles());
    $this->assertEquals(['bar'], $rendered->getScripts());
  }

  public function testResolvesVariables() {
    $twig = $this->prophesize(\Twig_Environment::class);
    $twig->render('twig-no-metadata.twig', ['foo' => 'bar - resolved'])->willReturn('rendered');

    $pattern = $this->getSupportedPattern();
    $setResolver = $this->prophesize(SetResolver::class);
    $setResolver->resolveSet($pattern->getVariableDefinition(), $pattern->getVariableSets()['default'])
      ->shouldBeCalled()
      ->willReturn(['foo' => 'bar - resolved']);

    $renderer = new TwigRenderer($twig->reveal(), $setResolver->reveal());
    $renderer->render($pattern, $pattern->getVariableSets()['default']);
  }

}