<?php


namespace LastCall\Patterns\Core\Parser;


use LastCall\Patterns\Core\Pattern\PatternInterface;

interface TemplateFileParserInterface {

  public function supports(\SplFileInfo $fileInfo): bool;

  public function parse(\SplFileInfo $fileInfo): PatternInterface;

}