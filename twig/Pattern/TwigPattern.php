<?php


namespace LastCall\Patterns\Twig\Pattern;


use LastCall\Patterns\Core\Pattern\AbstractPattern;
use LastCall\Patterns\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Patterns\Core\Variable\VariableSet;

class TwigPattern extends AbstractPattern implements TemplateFilePatternInterface {

  private $source;
  private $templateFile;

  public function __construct($id, \Twig_Source $source) {
    $this->id = $id;
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