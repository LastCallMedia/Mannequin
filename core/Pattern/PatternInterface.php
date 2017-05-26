<?php

namespace LastCall\Mannequin\Core\Pattern;

use LastCall\Mannequin\Core\Variable\Definition;
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
   * Set the tags on the pattern.
   *
   * @param array $tags
   *
   * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
   */
  public function setTags(array $tags): PatternInterface;

  public function getVariableDefinition(): Definition;

  public function getVariableSets(): array;

}