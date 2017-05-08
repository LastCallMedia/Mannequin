<?php


namespace LastCall\Patterns\Core\Variable;


interface VariableInterface {

  public static function getSupportedtypes(): array;

  public function __construct(string $typeName, $value);

  public function hasValue(): bool;

  public function getValue();

  public function getTypeName(): string;
}