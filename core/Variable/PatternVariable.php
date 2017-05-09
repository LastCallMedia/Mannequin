<?php


namespace LastCall\Patterns\Core\Variable;


class PatternVariable implements VariableInterface {

  private $render;

  public function __construct(callable $renderFn) {
    $this->render = $renderFn;
  }

  public function getTypeName(): string {
    return 'pattern';
  }

  public function getValue() {
    $renderer = $this->render;
    return $renderer();
  }

  public function hasValue(): bool {
    return TRUE;
  }
}