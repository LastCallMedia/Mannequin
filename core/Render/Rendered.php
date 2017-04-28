<?php

namespace LastCall\Patterns\Core\Render;

use LastCall\Patterns\Core\Pattern\PatternInterface;

class Rendered implements RenderedInterface {

  private $pattern;

  private $markup;

  public function __construct(PatternInterface $pattern) {
    $this->pattern = $pattern;
  }

  public function getPattern(): PatternInterface {
    return $this->pattern;
  }

  public function setMarkup(string $markup) {
    $this->markup = $markup;
  }

  public function getMarkup(): string {
    return $this->markup;
  }
}