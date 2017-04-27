<?php


namespace LastCall\Patterns\Core\Pattern;


trait HasNameAndId {

  private $id;
  private $name;

  private function setId(string $id) {
    $this->id = $id;
  }

  public function getId(): string {
    return $this->id;
  }

  private function setName(string $name) {
    $this->name = $name;
  }

  public function getName(): string {
    return $this->name;
  }
}