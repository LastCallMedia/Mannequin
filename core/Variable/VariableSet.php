<?php


namespace LastCall\Patterns\Core\Variable;


use LastCall\Patterns\Core\Exception\InvalidVariableException;

class VariableSet {

  private $data = [];

  public function __construct(array $variables = []) {
    foreach($variables as $key => $value) {
      $this->set($key, $value);
    }
  }

  public function has($key) {
    return isset($this->data[$key]);
  }

  public function set($key, VariableInterface $variable) {
    $this->data[$key] = $variable;
  }

  public function applyGlobals(VariableSet $globals) {
    $applied = [];
    foreach($this->data as $key => $value) {
      if(!$value->hasValue() && isset($globals->data[$key]) && $globals->data[$key]->hasValue()) {
        if($value->getTypeName() !== $globals->data[$key]->getTypeName()) {
          throw new InvalidVariableException(sprintf('Cannot merge sets - Expected %s to be an %s, got an %s', $key, $value->getTypeName(), $globals->data[$key]->getTypeName()));
        }
        $applied[$key] = $globals->data[$key];
      }
      else {
        $applied[$key] = $value;
      }
    }
    return new VariableSet($applied);
  }

  public function manifest() {
    $return = [];
    foreach($this->data as $key => $value) {
      if($value->hasValue()) {
        $return[$key] = $value->getValue();
      }
    }
    return $return;
  }
}