<?php


namespace LastCall\Mannequin\Core\Pattern;


use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;

abstract class AbstractPattern implements PatternInterface {

  protected $id;
  protected $aliases = [];
  private $name = '';
  private $description = '';
  private $tags = [];
  private $variableDefinition;
  private $variableSets = [];
  private $variableSet;

  public function __construct($id, array $aliases = []) {
    $this->id = $id;
    $this->aliases = $aliases;
    $this->variableSets['default'] = new Set('Default', []);
  }

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

  public function setVariableDefinition(Definition $definition): PatternInterface {
    $this->variableDefinition = $definition;
    return $this;
  }

  public function getVariableDefinition(): Definition {
    return $this->variableDefinition ?: new Definition([]);
  }

  public function addVariableSet($id, Set $set) {
    $this->variableSets[$id] = $set;
  }

  public function getVariableSets(): array {
    return $this->variableSets;
  }
}