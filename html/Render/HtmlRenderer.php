<?php


namespace LastCall\Patterns\Html\Render;


use LastCall\Patterns\Html\Pattern\HtmlPattern;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Render\RenderedInterface;
use LastCall\Patterns\Core\Render\RendererInterface;
use LastCall\Patterns\Core\Render\Rendered;
use LastCall\Patterns\Core\Variable\VariableSet;

class HtmlRenderer implements RendererInterface {

  public function __construct(array $styles = [], array $scripts = []) {
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof HtmlPattern;
  }

  public function render(PatternInterface $pattern, VariableSet $overrides = NULL): RenderedInterface {
    $rendered = new Rendered($pattern, $this->styles, $this->scripts);
    $rendered->setMarkup(file_get_contents($pattern->getFileInfo()->getPathname()));
    return $rendered;
  }

}