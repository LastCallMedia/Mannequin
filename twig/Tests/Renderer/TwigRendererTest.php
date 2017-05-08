<?php


namespace LastCall\Patterns\Twig\Tests\Renderer;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use LastCall\Patterns\Twig\Render\TwigRenderer;
use PHPUnit\Framework\TestCase;

class TwigRendererTest extends TestCase {

  private function getTwig() {
    $twig = $this->prophesize(\Twig_Environment::class);
    return $twig;
  }

  public function testDoesntSupportOtherPatterns() {
    $twig = $this->getTwig();
    $pattern = $this->prophesize(PatternInterface::class)->reveal();
    $renderer = new TwigRenderer($twig->reveal(), new VariableSet());
    $this->assertFalse($renderer->supports($pattern));
  }

  public function testSupportsTwigPatterns() {
    $twig = $this->getTwig();
    $pattern = $this->prophesize(TwigPattern::class)->reveal();
    $renderer = new TwigRenderer($twig->reveal(), new VariableSet());
    $this->assertTrue($renderer->supports($pattern));
  }

  public function testRendersTwigPattern() {
    $scripts = ['foo', 'bar'];
    $styles = ['bar', 'baz'];
    $variables = new VariableSet([]);

    $twig = $this->getTwig();
    $twig->render('foo', [])->willReturn('rendered');
    $pattern = new TwigPattern(
      'foo',
      'Foo',
      'foo',
      $variables
    );
    $renderer = new TwigRenderer($twig->reveal(), new VariableSet(), $styles, $scripts);
    $rendered = $renderer->render($pattern);
    $this->assertEquals('rendered', $rendered->getMarkup());
    $this->assertEquals($pattern, $rendered->getPattern());
    $this->assertEquals($scripts, $rendered->getScripts());
    $this->assertEquals($styles, $rendered->getStyles());
  }
}