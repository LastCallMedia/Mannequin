<?php


namespace LastCall\Mannequin\Core\Variable;


class ScalarType implements VariableInterface {

  private $value;
  private $typeName;

  public function __construct(string $typeName, $value = NULL) {
    $this->typeName = $typeName;
    $this->value = $value;
  }

  public function getTypeName(): string {
    return $this->typeName;
  }

  public function hasValue(): bool {
    return $this->value !== NULL;
  }

  public function getValue() {
    return $this->value;
  }
}