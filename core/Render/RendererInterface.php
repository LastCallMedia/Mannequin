<?php


namespace LastCall\Mannequin\Core\Render;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;

interface RendererInterface {

  public function supports(PatternInterface $pattern): bool;

  public function render(PatternInterface $pattern, VariableSet $overrides = NULL): RenderedInterface;

}