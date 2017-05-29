<?php


namespace LastCall\Mannequin\Core\Engine;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RenderedInterface;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\VariableSet;

interface EngineInterface {

  public function supports(PatternInterface $pattern): bool;

  public function render(PatternInterface $pattern, Set $set): RenderedInterface;

  public function renderSource(PatternInterface $pattern): string;

}