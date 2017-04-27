<?php

namespace LastCall\Patterns\Core\Ui;

use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\RenderedInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface UiInterface {

  public function decorateIndex(PatternCollection $collection, UrlGeneratorInterface $generator): string;

  public function decorateRendered(RenderedInterface $rendered): string;

}