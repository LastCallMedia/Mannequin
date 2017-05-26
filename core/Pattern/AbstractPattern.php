<?php


namespace LastCall\Mannequin\Core\Pattern;


use LastCall\Mannequin\Core\Variable\VariableSet;

abstract class AbstractPattern implements PatternInterface {

  protected $id;
  protected $aliases = [];
  private $name = '';
  private $description = '';
  private $tags = [];
  private $variableSet;

  /**
   * {@inheritdoc}
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getAliases(): array {
    return $this->aliases;
  }

  /**
   * {@inheritdoc}
   */
  public function setName(string $name): PatternInterface {
    $this->name = $name;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription(string $description): PatternInterface {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setTags(array $tags): PatternInterface {
    $this->tags = $tags;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTags(): array {
    return $this->tags;
  }

  /**
   * {@inheritdoc}
   */
  public function hasTag(string $name, $value): bool {
    return isset($this->tags[$name]) && $this->tags[$name] === $value;
  }

  /**
   * {@inheritdoc}
   */
  public function addTag(string $name, $value): PatternInterface {
    $this->tags[$name] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setVariables(VariableSet $variableSet): PatternInterface {
    $this->variableSet = $variableSet;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getVariables(): VariableSet {
    return $this->variableSet ? $this->variableSet : new VariableSet();
  }
}