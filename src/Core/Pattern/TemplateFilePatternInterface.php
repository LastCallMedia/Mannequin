<?php


namespace LastCall\Mannequin\Core\Pattern;


interface TemplateFilePatternInterface extends PatternInterface {

  public function getFile(): \SplFileInfo;

}