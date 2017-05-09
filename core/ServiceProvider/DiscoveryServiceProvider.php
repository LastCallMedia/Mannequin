<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Discovery\ChainDiscovery;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DiscoveryServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['discoverers'] = function() {
      return [];
    };
    $pimple['discovery'] = function() use ($pimple) {
      $discoverers = [];
      foreach($pimple['extensions'] as $extension) {
        $discoverers = array_merge($discoverers, $extension->getDiscoverers());
      }
      return new ChainDiscovery($discoverers);
    };
    // TODO: Implement register() method.
  }
}