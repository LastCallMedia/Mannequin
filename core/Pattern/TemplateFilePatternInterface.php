<?php


namespace LastCall\Patterns\Core\Pattern;


interface TemplateFilePatternInterface extends PatternInterface {

  public function getFile(): \SplFileInfo;

}