<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableSet;

interface RendererInterface {

  public function supports(PatternInterface $pattern): bool;

  public function render(PatternInterface $pattern, VariableSet $overrides = NULL): RenderedInterface;

}