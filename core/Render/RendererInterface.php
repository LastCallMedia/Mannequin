<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Pattern\PatternInterface;

interface RendererInterface {

  public function supports(PatternInterface $pattern): bool;

  public function render(PatternInterface $pattern): RenderedInterface;

}