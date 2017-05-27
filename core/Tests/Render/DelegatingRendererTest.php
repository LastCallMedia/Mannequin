<?php


namespace LastCall\Mannequin\Core\Tests\Render;



use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\DelegatingRenderer;
use LastCall\Mannequin\Core\Render\Rendered;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Variable\Set;
use Prophecy\Argument;

class DelegatingRendererTest extends RendererTestCase {

  public function getRenderer(): RendererInterface {
    $subrenderer = $this->prophesize(RendererInterface::class);
    $subrenderer->supports(Argument::type(PatternInterface::class))->will(function($args) {
      return $args[0]->getId() === 'supported';
    });
    $subrenderer->render(Argument::type(PatternInterface::class), Argument::type(Set::class))->will(function($args) {
      return new Rendered($args[0]);
    });
    $subrenderer->renderSource(Argument::type(PatternInterface::class))->willReturn('Test source');
    return new DelegatingRenderer([$subrenderer->reveal()]);
  }

  public function getSupportedPattern(): PatternInterface {
    return $this->createPattern('supported')->reveal();
  }
}