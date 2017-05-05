<?php


namespace LastCall\Patterns\Core\Pattern;


class HtmlPattern implements PatternInterface {

  use HasNameAndId;
  use Taggable;

  private $filename;

  public function __construct($id, $name, $filename) {
    $this->setId($id);
    $this->setName($name);
    $this->filename = $filename;
  }

  public function getFilename() {
    return $this->filename;
  }
}