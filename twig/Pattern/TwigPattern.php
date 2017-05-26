<?php


namespace LastCall\Mannequin\Twig\Pattern;


use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;

class TwigPattern extends AbstractPattern implements TemplateFilePatternInterface {

  private $source;
  private $templateFile;

  public function __construct($id, array $aliases, \Twig_Source $source) {
    $this->id = $id;
    $this->aliases = $aliases;
    $this->source = $source;
    $this->templateFile = new \SplFileInfo($source->getPath());
  }

  public function getSource() {
    return $this->source;
  }

  public function getFile(): \SplFileInfo {
    return $this->templateFile;
  }
}