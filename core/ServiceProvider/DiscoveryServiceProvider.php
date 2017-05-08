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
      return new ChainDiscovery($pimple['discoverers']);
    };
    // TODO: Implement register() method.
  }
}