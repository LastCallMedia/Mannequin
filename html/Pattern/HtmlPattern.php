<?php


namespace LastCall\Patterns\Html\Pattern;


use LastCall\Patterns\Core\Variable\VariableSet;
use LastCall\Patterns\Core\Pattern\HasNameAndId;
use LastCall\Patterns\Core\Pattern\Taggable;
use LastCall\Patterns\Core\Pattern\PatternInterface;

class HtmlPattern implements PatternInterface {

  use HasNameAndId;
  use Taggable;

  private $fileInfo;
  private $variables;

  public function __construct($id, $name, \SplFileInfo $fileInfo) {
    $this->setId($id);
    $this->setName($name);
    $this->fileInfo = $fileInfo;
    $this->variables = new VariableSet();
  }

  public function getFileInfo() {
    return $this->fileInfo;
  }

  public function getVariables(): VariableSet {
    return $this->variables;
  }
}