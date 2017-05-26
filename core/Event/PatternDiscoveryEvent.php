<?php


namespace LastCall\Mannequin\Core\Event;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\EventDispatcher\Event;

class PatternDiscoveryEvent extends Event {

  public function __construct(PatternInterface $pattern) {
    $this->pattern = $pattern;
  }

  public function getPattern(): PatternInterface {
    return $this->pattern;
  }

}