<?php


namespace LastCall\Patterns\Core\Tests\Render;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Render\EngineInterface;
use LastCall\Patterns\Core\Render\Renderer;
use LastCall\Patterns\Core\RenderedInterface;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase {

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage Unable to find a compatible render engine for foo
   */
  public function testRenderPatternWithIncompatibleEngine() {
    $pattern = $this->prophesize(PatternInterface::class);
    $pattern->getName()->willReturn('foo');

    $engine = $this->prophesize(EngineInterface::class);
    $engine->supports($pattern)->willReturn(FALSE);

    $renderer = new Renderer([$engine->reveal()]);
    $renderer->renderPattern($pattern->reveal());
  }

  public function testRenderPattern() {
    $pattern = $this->prophesize(PatternInterface::class);
    $pattern->getName()->willReturn('foo');

    $rendered = $this->prophesize(RenderedInterface::class)->reveal();

    $engine = $this->prophesize(EngineInterface::class);
    $engine->supports($pattern)->willReturn(TRUE);
    $engine->render($pattern, [])->willReturn($rendered);

    $renderer = new Renderer([$engine->reveal()]);
    $actual = $renderer->renderPattern($pattern->reveal());
    $this->assertEquals($rendered, $actual);
  }
}