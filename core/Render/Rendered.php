<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Pattern\HasNameAndId;

class Rendered implements RenderedInterface {

  use HasNameAndId;

  private $markup;

  public function __construct($id, $name) {
    $this->setId($id);
    $this->setName($name);
  }

  public function setMarkup(string $markup) {
    $this->markup = $markup;
  }

  public function getMarkup(): string {
    return $this->markup;
  }
}