<?php


namespace LastCall\Mannequin\Html\Render;


use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RenderedInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Render\Rendered;

class HtmlRenderer implements RendererInterface {

  public function __construct(array $styles = [], array $scripts = []) {
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof HtmlPattern;
  }

  public function render(PatternInterface $pattern, Set $set): RenderedInterface {
    if($this->supports($pattern)) {
      $rendered = new Rendered($pattern);
      $rendered->setMarkup(file_get_contents($pattern->getFile()->getPathname()));
      $rendered->setStyles($this->styles);
      $rendered->setScripts($this->scripts);
      return $rendered;
    }
    throw new UnsupportedPatternException('Unsupported Pattern.');
  }

  public function renderSource(PatternInterface $pattern): string {
    if($this->supports($pattern)) {
      return file_get_contents($pattern->getFile()->getPathname());
    }
    throw new UnsupportedPatternException('Unsupported pattern.');
  }

}