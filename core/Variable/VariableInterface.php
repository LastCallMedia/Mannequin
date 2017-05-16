<?php


namespace LastCall\Mannequin\Core\Variable;


interface VariableInterface {

  public function hasValue(): bool;

  public function getValue();

  public function getTypeName(): string;
}