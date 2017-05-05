<?php


namespace LastCall\Patterns\Core\Discovery;


use LastCall\Patterns\Core\Pattern\PatternCollection;

class ChainDiscovery implements DiscoveryInterface {

  private $discoverers = [];

  public function __construct(array $discoverers = []) {
    foreach($discoverers as $discoverer) {
      if(!$discoverer instanceof DiscoveryInterface) {
        throw new \InvalidArgumentException(sprintf('Discoverer must implement %s', DiscoveryInterface::class));
      }
      $this->discoverers[] = $discoverer;
    }
  }

  public function discover(): PatternCollection {
    $rootCollection = new PatternCollection();
    foreach($this->discoverers as $discoverer) {
      $rootCollection = $rootCollection->merge($discoverer->discover());
    }
    return $rootCollection;
  }
}