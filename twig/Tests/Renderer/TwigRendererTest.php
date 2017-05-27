<?php


namespace LastCall\Mannequin\Twig\Tests\Renderer;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Render\TwigRenderer;
use PHPUnit\Framework\TestCase;

class TwigRendererTest extends TestCase {

  private function getTwig() {
    $twig = $this->prophesize(\Twig_Environment::class);
    return $twig;
  }

  public function testDoesntSupportOtherPatterns() {
    $twig = $this->getTwig();
    $pattern = $this->prophesize(PatternInterface::class)->reveal();
    $renderer = new TwigRenderer($twig->reveal(), new SetResolver());
    $this->assertFalse($renderer->supports($pattern));
  }

  public function testSupportsTwigPatterns() {
    $twig = $this->getTwig();
    $pattern = $this->prophesize(TwigPattern::class)->reveal();
    $renderer = new TwigRenderer($twig->reveal(), new SetResolver());
    $this->assertTrue($renderer->supports($pattern));
  }

  public function testAddsGlobalScriptsAndStyles() {
    $scripts = ['foo', 'bar'];
    $styles = ['bar', 'baz'];
    $twig = $this->getTwig();
    $twig->render('foo', [])->willReturn('rendered');
    $pattern = new TwigPattern('foo', [], new \Twig_Source('', 'foo', 'foo'));
    $renderer = new TwigRenderer($twig->reveal(), new SetResolver(), $styles, $scripts);
    $rendered = $renderer->render($pattern, new Set('default'));
    $this->assertEquals($scripts, $rendered->getScripts());
  }

  public function testResolvesVariables() {
    $definition = new Definition(['foo' => 'string']);
    $set = new Set('Default', ['foo' => 'baz']);

    $twig = $this->getTwig();
    $twig->render('foo', ['foo' => 'bar - resolved'])->willReturn('rendered');
    $pattern = new TwigPattern('foo', [], new \Twig_Source('', 'foo', 'foo'));
    $pattern->setVariableDefinition($definition);

    $setResolver = $this->prophesize(SetResolver::class);
    $setResolver->resolveSet($definition, $set)
      ->shouldBeCalled()
      ->willReturn(['foo' => 'bar - resolved']);

    $renderer = new TwigRenderer($twig->reveal(), $setResolver->reveal());
    $renderer->render($pattern, $set);
  }

}