<?php

namespace LastCall\Patterns\Core\Pattern;

use LastCall\Patterns\Core\Variable\VariableSet;

interface PatternInterface {

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
  public function addTag(string $name, $value);

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