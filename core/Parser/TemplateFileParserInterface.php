<?php


namespace LastCall\Patterns\Core\Parser;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use Symfony\Component\Finder\SplFileInfo;

interface TemplateFileParserInterface {

  /**
   * Check whether this parser supports parsing the file.
   *
   * @param \Symfony\Component\Finder\SplFileInfo $fileInfo
   *
   * @return bool
   */
  public function supports(SplFileInfo $fileInfo): bool;

  /**
   * Parse a template file into a Pattern object.
   *
   * @param \Symfony\Component\Finder\SplFileInfo $fileInfo
   *
   * @return \LastCall\Patterns\Core\Pattern\PatternInterface
   *
   * @throws \LastCall\Patterns\Core\Exception\TemplateParsingException
   */
  public function parse(SplFileInfo $fileInfo): PatternInterface;

}