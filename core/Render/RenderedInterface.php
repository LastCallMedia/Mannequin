<?php

namespace LastCall\Mannequin\Core\Render;

use LastCall\Mannequin\Core\Pattern\PatternInterface;

interface RenderedInterface {

  public function getPattern(): PatternInterface;
  public function getMarkup(): string;
  public function getStyles() : array;
  public function getScripts(): array;
  public function addScripts(array $scripts);
  public function addStyles(array $styles);
}