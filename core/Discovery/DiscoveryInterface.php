<?php


namespace LastCall\Mannequin\Core\Discovery;


use LastCall\Mannequin\Core\Pattern\PatternCollection;

interface DiscoveryInterface {
  public function discover(): PatternCollection;
}