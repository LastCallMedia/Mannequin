<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Variable\ScalarType;
use LastCall\Patterns\Core\Variable\VariableFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class VariableServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['variable.types'] = function() {
      return [
        ScalarType::class
      ];
    };
    $pimple['variable.factory'] = function() use ($pimple) {
      return new VariableFactory($pimple['variable.types']);
    };
  }

}