<?php


namespace LastCall\Mannequin\Cli\Templating;


use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReference;
use Symfony\Component\Templating\TemplateReferenceInterface;

class PresetEngineTemplateNameParser implements TemplateNameParserInterface {

  private $engine;

  private $extension;

  public function __construct(string $engine, string $extension) {
    $this->engine = $engine;
    $this->extension = $extension;
  }

  public function parse($name) {
    if($name instanceof TemplateReferenceInterface) {
      return $name;
    }

    return new TemplateReference($name . '.' . $this->extension, $this->engine);
  }
}