<?php


namespace LastCall\Mannequin\Html\Render;


use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RenderedInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Render\Rendered;
use LastCall\Mannequin\Core\Variable\VariableSet;

class HtmlRenderer implements RendererInterface {

  public function __construct(array $styles = [], array $scripts = []) {
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof HtmlPattern;
  }

  public function render(PatternInterface $pattern, VariableSet $overrides = NULL): RenderedInterface {
    $rendered = new Rendered($pattern);
    $rendered->setMarkup(file_get_contents($pattern->getFile()->getPathname()));
    $rendered->setStyles($this->styles);
    $rendered->setScripts($this->scripts);
    return $rendered;
  }

}