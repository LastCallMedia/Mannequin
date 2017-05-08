<?php


namespace LastCall\Patterns\Twig\Pattern;


use LastCall\Patterns\Core\Pattern\HasNameAndId;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Pattern\Taggable;
use LastCall\Patterns\Core\Variable\VariableSet;

class TwigPattern implements PatternInterface {

  use HasNameAndId;

  use Taggable;

  private $filename;
  private $variables = [];

  public function __construct($id, $name, $filename, VariableSet $variables = NULL) {
    $this->setId($id);
    $this->setName($name);
    $this->filename = $filename;
    $this->variables = $variables ?: new VariableSet();
  }

  public function getFilename() {
    return $this->filename;
  }

  public function getVariables(): VariableSet {
    return $this->variables;
  }
}