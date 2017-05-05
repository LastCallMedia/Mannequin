<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Pattern\HtmlPattern;
use LastCall\Patterns\Core\Pattern\PatternInterface;

class HtmlRenderer implements RendererInterface {

  public function __construct(array $styles = [], array $scripts = []) {
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof HtmlPattern;
  }

  public function render(PatternInterface $pattern): RenderedInterface {
    $rendered = new Rendered($pattern, $this->styles, $this->scripts);
    $rendered->setMarkup(file_get_contents($pattern->getFilename()));
    return $rendered;
  }

}