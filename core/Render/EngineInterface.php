<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\RenderedInterface;

interface EngineInterface {

  /**
   * Indicate whether this engine supports a particular pattern.
   *
   * @param \LastCall\Patterns\Core\Pattern\PatternInterface $pattern
   *
   * @return bool
   */
  public function supports(PatternInterface $pattern): bool;

  /**
   * Render a given pattern.
   *
   * @param \LastCall\Patterns\Core\Pattern\PatternInterface $pattern
   * @param \Pimple\Container                             $variables
   *
   * @return \LastCall\Patterns\Core\RenderedInterface
   */
  public function render(PatternInterface $pattern, $variables, RenderedInterface $rendered): void;
}