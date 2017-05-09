<?php


namespace LastCall\Patterns\Core\Variable;


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
    $render = $this->renderFn;
    return new PatternVariable(function() use ($render, $value) {
      return $render($value);
    });
  }
}