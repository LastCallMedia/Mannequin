<?php

namespace LastCall\Mannequin\Core\Pattern;

use LastCall\Mannequin\Core\Variable\VariableSet;

interface PatternInterface {

  public function setName(string $name): PatternInterface;

  public function setTags(array $tags): PatternInterface;

  public function setVariables(VariableSet $variableSet): PatternInterface;

  /**
   * Get the name of the pattern.
   *
   * @return string
   */
  public function getName(): string;

  /**
   * Get the unique identifier for the pattern.
   *
   * @return string
   */
  public function getId(): string;

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
   * Check whether the pattern has a given tag.
   *
   * @param $name
   * @param $value
   *
   * @return bool
   */
  public function hasTag(string $name, $value): bool;

  public function getTags(): array;

  public function getVariables(): VariableSet;

}