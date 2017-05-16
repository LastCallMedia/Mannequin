<?php


namespace LastCall\Mannequin\Core\Variable;


class PatternFactory implements VariableFactoryInterface {

  public function __construct(callable $renderFn) {
    $this->renderFn = $renderFn;
  }

  public function getTypes(): array {
    return ['pattern'];
  }

  public function hasType($type): bool {
    return $type === 'pattern';
  }

  public function create($type, $value = NULL): VariableInterface {
    if(is_string($value)) {
      $id = $value;
      $overrides = new VariableSet();
    }
    elseif(is_array($value)) {
      $id = $value['id'];
      $overrides = $value['variables'];
    }
    return new PatternVariable($this->renderFn, $id, $overrides);
  }
}