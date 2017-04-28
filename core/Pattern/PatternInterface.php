<?php

namespace LastCall\Patterns\Core\Pattern;

interface PatternInterface {

  /**
   * @return string
   */
  public function getName(): string;

  /**
   * @return string
   */
  public function getId(): string;

  public function addTag($type, $value): PatternInterface;

  public function hasTag($type, $value): bool;

  public function getTemplateReference(): string;

  public function getTemplateVariables();
}