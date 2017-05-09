<?php

namespace LastCall\Patterns\Core\Render;

use LastCall\Patterns\Core\Pattern\PatternInterface;

interface RenderedInterface {

  public function getPattern(): PatternInterface;
  public function getMarkup(): string;
  public function getStyles() : array;
  public function getScripts(): array;
  public function addScripts(array $scripts);
  public function addStyles(array $styles);
}