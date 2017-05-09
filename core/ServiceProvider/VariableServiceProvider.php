<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Variable\PatternFactory;
use LastCall\Patterns\Core\Variable\ScalarFactory;
use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Core\Variable\VariableSet;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class VariableServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['variable.types'] = function() {
      return [];
    };
    $pimple['variable.factories'] = function() use ($pimple) {
      return [
        'scalar' => new ScalarFactory(),
        'pattern' => new PatternFactory(function($id) use($pimple) {
          $pattern = $pimple['collection']->get($id);
          return $pimple['renderer']->render($pattern);
        })
      ];
    };
    $pimple['variables.global'] = function() {
      return new VariableSet();
    };
    $pimple['variable.factory'] = function() use ($pimple) {
      return new VariableFactory($pimple['variable.types'], $pimple['variable.factories']);
    };
  }

}