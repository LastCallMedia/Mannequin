<?php


namespace LastCall\Patterns\Twig\Parser;


use LastCall\Patterns\Core\Parser\TemplateFileParserInterface;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Pattern\TemplatePattern;
use LastCall\Patterns\Twig\Pattern\TwigPattern;

class TwigParser implements TemplateFileParserInterface {

  public function supports(\SplFileInfo $fileInfo): bool {
    return $fileInfo->getExtension() === 'twig';
  }

  public function parse(\SplFileInfo $fileInfo): PatternInterface {
    $name = $fileInfo->getBasename('.'.$fileInfo->getExtension());

    return new TwigPattern($name, ucfirst($name), $fileInfo->getPathname());
  }
}