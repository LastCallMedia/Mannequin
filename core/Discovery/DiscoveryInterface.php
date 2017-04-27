<?php


namespace LastCall\Patterns\Core\Discovery;


use LastCall\Patterns\Core\Pattern\PatternCollection;

interface DiscoveryInterface {
  public function discover(): PatternCollection;
}