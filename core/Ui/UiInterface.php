<?php

namespace LastCall\Patterns\Core\Ui;

use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\RenderedInterface;

interface UiInterface {

  public function decorateIndex(PatternCollection $collection): string;

  public function decorateRendered(RenderedInterface $rendered): string;

}