<?php


namespace LastCall\Mannequin\Core\Variable;


class Set {
  private $name;
  private $values;


  public function __construct(string $name, array $values = []) {
    $this->name = $name;
    $this->values = $values;
  }

  public function has(string $name) {
    return isset($this->values[$name]);
  }

  public function get(string $name) {
    return $this->values[$name];
  }
}