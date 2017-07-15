<?php


namespace LastCall\Mannequin\Twig\Pattern;


use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;

class TwigPattern extends AbstractPattern implements TemplateFilePatternInterface {

  private $source;

  public function __construct($id, array $aliases = [], \Twig_Source $source) {
    parent::__construct($id, $aliases);
    $this->aliases = $aliases;
    $this->source = $source;
  }

  public function getSource() {
    return $this->source;
  }

  public function getFile(): \SplFileInfo {
    return new \SplFileInfo($this->source->getPath());
  }

  public function getRawFormat(): string {
    return 'twig';
  }
}