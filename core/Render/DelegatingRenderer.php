<?php


namespace LastCall\Mannequin\Core\Render;


use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\VariableSet;

class DelegatingRenderer implements RendererInterface {

  private $renderers = [];

  public function __construct(array $renderers = []) {
    foreach($renderers as $renderer) {
      if(!$renderer instanceof RendererInterface) {
        throw new \InvalidArgumentException('Renderer must implement RendererInterface.');
      }
    }
    $this->renderers = $renderers;
  }

  public function supports(PatternInterface $pattern): bool {
    foreach($this->renderers as $renderer) {
      if($renderer->supports($pattern)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function render(PatternInterface $pattern, Set $set): RenderedInterface {
    foreach($this->renderers as $renderer) {
      if($renderer->supports($pattern)) {
        return $renderer->render($pattern, $set);
      }
    }
    throw new UnsupportedPatternException(sprintf('Unable to find a renderer for %s', get_class($pattern)));
  }

  public function renderSource(PatternInterface $pattern): string {
    foreach($this->renderers as $renderer) {
      if($renderer->supports($pattern)) {
        return $renderer->renderSource($pattern);
      }
    }
    throw new UnsupportedPatternException(sprintf('Unable to find a renderer for %s', get_class($pattern)));
  }
}