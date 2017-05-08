<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Variable\ScalarType;
use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Core\Variable\VariableSet;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class VariableServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['variable.types'] = function() {
      return [
        ScalarType::class
      ];
    };
    $pimple['variables.global'] = function() {
      return new VariableSet([
        'global' => new ScalarType('boolean', TRUE)
      ]);
    };
    $pimple['variable.factory'] = function() use ($pimple) {
      return new VariableFactory($pimple['variable.types']);
    };
  }

}