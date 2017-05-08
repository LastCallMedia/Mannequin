<?php


namespace LastCall\Patterns\Core\Variable;


interface VariableFactoryInterface {

  public function create($type, $value = NULL): VariableInterface;

  public function hasType($type): bool;

  public function getTypes(): array;

}