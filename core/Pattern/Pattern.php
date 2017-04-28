<?php


namespace LastCall\Patterns\Core\Pattern;


class Pattern implements PatternInterface {

  use HasNameAndId;

  private $reference;

  private $variables = [];

  private $tags = [];

  public function __construct($id, $name, $reference, $variables = []) {
    $this->setId($id);
    $this->setName($name);
    $this->reference = $reference;
    $this->variables = $variables;
  }

  public function addTag($type, $value): PatternInterface {
    $this->tags[$type][] = $value;
    return $this;
  }

  public function hasTag($type, $value): bool {
    return isset($this->tags[$type]) && (array_search($value, $this->tags[$type]) !== FALSE);
  }

  public function getTags() {
    return $this->tags;
  }

  public function getTemplateReference(): string {
    return $this->reference;
  }

  public function getTemplateVariables() {
    return $this->variables;
  }
}