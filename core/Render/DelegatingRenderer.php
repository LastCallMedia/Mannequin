<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Exception\UnsupportedPatternException;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableSet;

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

  public function render(PatternInterface $pattern, VariableSet $overrides = NULL): RenderedInterface {
    foreach($this->renderers as $renderer) {
      if($renderer->supports($pattern)) {
        return $renderer->render($pattern, $overrides);
      }
    }
    throw new UnsupportedPatternException(sprintf('Unable to find a renderer for %s', get_class($pattern)));
  }
}