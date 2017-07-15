<?php


namespace LastCall\Mannequin\Core\Variable;


class Definition {

  private $definitions = [];

  public function __construct(array $definitions = []) {
    foreach($definitions as $name => $type) {
      $this->definitions[$name] = $type;
    }
  }

  public function has($name) {
    return isset($this->definitions[$name]);
  }

  public function get($name) {
    return $this->definitions[$name];
  }

  public function keys() {
    return array_keys($this->definitions);
  }
}