<?php


namespace LastCall\Patterns\Cli\Templating\Helper;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

class UrlHelper extends Helper {

  private $generator;

  public function __construct(UrlGeneratorInterface $generator) {
    $this->generator = $generator;
  }

  public function getName() {
    return 'url';
  }

  public function generate($name, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
    return $this->generator->generate($name, $parameters, $referenceType);
  }
}