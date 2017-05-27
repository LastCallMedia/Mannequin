<?php


namespace LastCall\Mannequin\Html\Pattern;


use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;

class HtmlPattern extends AbstractPattern implements TemplateFilePatternInterface {

  private $fileInfo;

  public function __construct($id, array $aliases, \SplFileInfo $fileInfo) {
    parent::__construct($id, $aliases);
    $this->fileInfo = $fileInfo;
  }

  public function getFile(): \SplFileInfo {
    return $this->fileInfo;
  }
}