<?php


namespace LastCall\Mannequin\Drupal\Extension;


use LastCall\Mannequin\Drupal\Twig\DrupalTwigExtension;
use LastCall\Mannequin\Twig\Extension\TwigExtension;

class DrupalExtension extends TwigExtension {

  public function __construct(array $config = []) {
    parent::__construct($config);
    $this->addExtension(new DrupalTwigExtension());
  }
}