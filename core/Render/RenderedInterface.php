<?php

namespace LastCall\Patterns\Core\Render;

use LastCall\Patterns\Core\Pattern\PatternInterface;

interface RenderedInterface {

  public function getPattern(): PatternInterface;
  public function getMarkup(): string;
}