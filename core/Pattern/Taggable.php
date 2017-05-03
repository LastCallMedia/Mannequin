<?php


namespace LastCall\Patterns\Core\Pattern;


trait Taggable {

  private $tags = [];

  public function addTag(string $name, $value) {
    $this->tags[$name] = $value;
  }

  public function hasTag(string $name, $value): bool {
    return isset($this->tags[$name]) && $this->tags[$name] === $value;
  }

  public function getTags(): array {
    return $this->tags;
  }

}