<?php

namespace LastCall\Mannequin\Core\Pattern;

use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\VariableSet;

interface PatternInterface {

  /**
   * Get the unique identifier for the pattern.
   *
   * @return string
   */
  public function getId(): string;

  /**
   * Get other unique identifiers this pattern is known by.
   *
   * @return array
   */
  public function getAliases(): array;

  /**
   * Get the human readable name of the pattern.
   *
   * @return string
   */
  public function getName(): string;

  /**
   * Set the human readable name of the pattern.
   *
   * @param string $name
   *
   * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
   */
  public function setName(string $name): PatternInterface;

  /**
   * Get the description of the pattern.
   *
   * @return string
   */
  public function getDescription(): string;

  /**
   * Set the pattern description.
   *
   * @param string $description
   *
   * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
   */
  public function setDescription(string $description): PatternInterface;

  /**
   * Get all the tags on the pattern.
   *
   * @return array
   */
  public function getTags(): array;

  /**
   * Check whether the pattern has a given tag.
   *
   * @param $name
   * @param $value
   *
   * @return bool
   */
  public function hasTag(string $name, $value): bool;

  /**
   * Add a new tag to the pattern.
   *
   * @param $name
   * @param $value
   *
   * @return mixed
   */
  public function addTag(string $name, $value): PatternInterface;

  /**
   * Get the variable definitions for this pattern.
   *
   * @return \LastCall\Mannequin\Core\Variable\Definition
   */
  public function getVariableDefinition(): Definition;

  /**
   * Set the variable definition for this pattern.
   *
   * @param \LastCall\Mannequin\Core\Variable\Definition $definition
   *
   * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
   */
  public function setVariableDefinition(Definition $definition): PatternInterface;

  /**
   * Get the variable sets for this pattern.
   *
   * @return \LastCall\Mannequin\Core\Variable\Set[]
   */
  public function getVariableSets(): array;

  /**
   * Add a variable set for this pattern.
   *
   * @param \LastCall\Mannequin\Core\Variable\Set $set
   *
   * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
   */
  public function addVariableSet(string $id, Set $set): PatternInterface;


  public function addUsedPattern(PatternInterface $pattern): PatternInterface;

  public function getUsedPatterns(): array;

  public function getRawFormat(): string;
}