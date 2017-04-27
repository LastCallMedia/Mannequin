<?php


namespace LastCall\Patterns\Cli;


use LastCall\Patterns\Core\Pattern\PatternInterface;

class PatternRouter {

  private $routePattern;

  public function __construct(string $routePattern) {
    $this->routePattern = $routePattern;
  }

  public function getRoute(PatternInterface $pattern) {

  }
}