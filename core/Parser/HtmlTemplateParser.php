<?php


namespace LastCall\Patterns\Core\Parser;


use LastCall\Patterns\Core\Pattern\HtmlPattern;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use Symfony\Component\Finder\SplFileInfo;

class HtmlTemplateParser implements TemplateFileParserInterface {

  public function supports(SplFileInfo $fileInfo): bool {
    return $fileInfo->getExtension() === 'html';
  }

  public function parse(SplFileInfo $fileInfo): PatternInterface {
    $filename = $fileInfo->getBasename('.'.$fileInfo->getExtension());

    $pattern = new HtmlPattern($filename, ucfirst($filename), $fileInfo->getPathname());
    $pattern->addTag('renderer', 'HTML');
    return $pattern;
  }
}