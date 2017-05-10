<?php


namespace LastCall\Patterns\Drupal\Extension;


use LastCall\Patterns\Drupal\Twig\DrupalTwigExtension;
use LastCall\Patterns\Twig\Extension\TwigExtension;

class DrupalExtension extends TwigExtension {

  public function __construct(array $config = []) {
    parent::__construct($config);
    $this->addExtension(new DrupalTwigExtension());
  }
}