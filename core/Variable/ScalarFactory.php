<?php


namespace LastCall\Mannequin\Core\Variable;


class ScalarFactory implements VariableFactoryInterface {

  private static $supportedTypes = [
    'integer',
    'boolean',
    'string',
  ];

  public function create($type, $value = NULL): VariableInterface {
    return new ScalarType($type, $value);
  }

  public function hasType($type): bool {
    return in_array($type, $this::$supportedTypes);
  }

  public function getTypes(): array {
    return static::$supportedTypes;
  }
}