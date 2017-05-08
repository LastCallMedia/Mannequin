<?php


namespace LastCall\Patterns\Core\Pattern;


use LastCall\Patterns\Core\Variable\VariableSet;

class HtmlPattern implements PatternInterface {

  use HasNameAndId;
  use Taggable;

  private $filename;
  private $variables;

  public function __construct($id, $name, $filename) {
    $this->setId($id);
    $this->setName($name);
    $this->filename = $filename;
    $this->variables = new VariableSet();
  }

  public function getFilename() {
    return $this->filename;
  }

  public function getVariables(): VariableSet {
    return $this->variables;
  }
}