<?php


namespace LastCall\Patterns\Html\Pattern;


use LastCall\Patterns\Core\Pattern\AbstractPattern;
use LastCall\Patterns\Core\Pattern\TemplateFilePatternInterface;

class HtmlPattern extends AbstractPattern implements TemplateFilePatternInterface {

  private $fileInfo;

  public function __construct($id, \SplFileInfo $fileInfo) {
    $this->id = $id;
    $this->fileInfo = $fileInfo;
  }

  public function getFile(): \SplFileInfo {
    return $this->fileInfo;
  }
}