<?php


namespace LastCall\Patterns\Core\Variable;


class PatternVariable implements VariableInterface {

  private $render;

  public function __construct(callable $renderFn, $id, VariableSet $overrides) {
    $this->render = $renderFn;
    $this->id = $id;
    $this->overrides = $overrides;
  }

  public function getTypeName(): string {
    return 'pattern';
  }

  public function getValue() {
    $renderer = $this->render;
    return $renderer($this->id, $this->overrides);
  }

  public function hasValue(): bool {
    return TRUE;
  }
}