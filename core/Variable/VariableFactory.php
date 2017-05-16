<?php


namespace LastCall\Mannequin\Core\Variable;


use LastCall\Mannequin\Core\Exception\InvalidVariableException;

class VariableFactory implements VariableFactoryInterface {

  private $types;

  public function __construct(array $types = [], array $factories = []) {
    foreach($types as $name => $type) {
      $this->addType($name, $type);
    }
    foreach($factories as $factory) {
      $this->addFactory($factory);
    }
  }

  public function addType($type, callable $factory) {
    $this->types[$type] = $factory;
  }

  public function hasType($type): bool {
    return isset($this->types[$type]);
  }

  public function getTypes(): array {
    return array_keys($this->types);
  }

  public function addFactory(VariableFactoryInterface $factory) {
    foreach($factory->getTypes() as $type) {
      $this->addType($type, function($value) use($factory, $type) {
        return $factory->create($type, $value);
      });
    }
  }

  public function create($type, $value = NULL): VariableInterface {
    if(isset($this->types[$type])) {
      $callable = $this->types[$type];
      return $callable($value);
    }
    throw new InvalidVariableException(sprintf('%s is not a valid variable type', $type));
  }
}