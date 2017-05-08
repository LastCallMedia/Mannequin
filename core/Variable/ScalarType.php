<?php


namespace LastCall\Patterns\Core\Variable;


class ScalarType implements VariableInterface {

  private $value;
  private $typeName;

  public static function getSupportedTypes(): array {
    return ['string', 'integer', 'boolean'];
  }

  public function __construct(string $typeName, $value) {
    if(!in_array($typeName, $this->getSupportedTypes())) {
      throw new \RuntimeException(sprintf('%s created with invalid $typeName %s', static::class, $typeName));
    }
    $this->typeName = $typeName;
    $this->value = $value;
  }

  public function getTypeName(): string {
    return $this->typeName;
  }

  public function getValue() {
    switch($this->typeName) {
      case 'boolean':
        return (bool) $this->value;
      case 'string':
        return (string) $this->value;
      case 'integer':
        return (string) $this->value;
    }
  }
}