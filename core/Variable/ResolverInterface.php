<?php


namespace LastCall\Mannequin\Core\Variable;


interface ResolverInterface {

  public function resolves(string $type): bool;

  public function validate(string $type, $value);

  public function resolve(string $type, $value);

}