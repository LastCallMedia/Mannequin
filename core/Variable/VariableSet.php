<?php


namespace LastCall\Patterns\Core\Variable;


use LastCall\Patterns\Core\Exception\InvalidVariableException;

class VariableSet {

  private $data = [];

  public function __construct(array $variables = []) {
    foreach($variables as $key => $value) {
      if(!$value instanceof VariableInterface) {
        throw new InvalidVariableException(sprintf('Variable %s passed to set must implement %s', $key, VariableInterface::class));
      }
      $this->data[$key] = $value;
    }
  }

  public function has($key) {
    return isset($this->data[$key]);
  }

  public function merge(VariableSet $toMerge) {
    foreach($this->data as $key => $value) {
      if(isset($toMerge->data[$key])) {
        // Check that what we're going to merge in is of the same type.
        if(get_class($value) !== get_class($toMerge->data[$key])) {
          throw new InvalidVariableException(sprintf('Cannot merge sets - %s is of a different class', $key));
        }
        if($value->getTypeName() !== $toMerge->data[$key]->getTypeName()) {
          throw new InvalidVariableException(sprintf('Cannot merge sets - %s is of a different type', $key));
        }
      }
    }
    return new VariableSet($toMerge->data +$this->data);
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