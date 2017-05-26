<?php


namespace LastCall\Mannequin\Core\Variable;


interface VariableFactoryInterface {

  public function provides(string $type): bool;

  public function validate(string $type, $value);

  public function realize(string $type, $value);

}