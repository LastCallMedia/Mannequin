<?php


namespace LastCall\Patterns\Core\Variable;


use LastCall\Patterns\Core\Exception\InvalidVariableException;

class VariableFactory {

  private $types;

  public function __construct(array $typeClasses = []) {
    foreach($typeClasses as $typeClass) {
      $this->addTypeClass($typeClass);
    }
  }

  public function addTypeClass($className) {
    if(!class_exists($className)) {
      throw new \RuntimeException(sprintf('%s does not exist', $className));
    }
    if(!is_a($className, VariableInterface::class, TRUE)) {
      throw new \RuntimeException(sprintf('%s does not implement %s', $className, VariableInterface::class));
    }
    foreach($className::getSupportedTypes() as $type) {
      $this->types[$type] = $className;
    }
  }

  public function hasType($type) {
    return isset($this->types[$type]);
  }

  public function create($type, $value): VariableInterface {
    if(isset($this->types[$type])) {
      $class = $this->types[$type];
      return new $class($type, $value);
    }
    throw new InvalidVariableException(sprintf('%s is not a valid variable type', $type));
  }
}