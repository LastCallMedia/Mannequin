<?php


namespace LastCall\Mannequin\Core\Pattern;


use LastCall\Mannequin\Core\Variable\VariableSet;

abstract class AbstractPattern implements PatternInterface {

  protected $id;
  private $name = '';
  private $description = '';
  private $tags = [];
  private $variableSet;

  public function getId(): string {
    return $this->id;
  }

  public function setName(string $name): PatternInterface {
    $this->name = $name;
    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setDescription(string $description): PatternInterface {
    $this->description = $description;
    return $this;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setTags(array $tags): PatternInterface {
    $this->tags = $tags;
    return $this;
  }

  public function getTags(): array {
    return $this->tags;
  }

  public function hasTag(string $name, $value): bool {
    return isset($this->tags[$name]) && $this->tags[$name] === $value;
  }

  public function addTag(string $name, $value): PatternInterface {
    $this->tags[$name] = $value;
    return $this;
  }

  public function setVariables(VariableSet $variableSet): PatternInterface {
    $this->variableSet = $variableSet;
    return $this;
  }

  public function getVariables(): VariableSet {
    return $this->variableSet ? $this->variableSet : new VariableSet();
  }
}