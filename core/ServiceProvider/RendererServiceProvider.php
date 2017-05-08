<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Render\DelegatingRenderer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RendererServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['renderers'] = function() {
      return [];
    };
    $pimple['renderer'] = function() use($pimple) {
      return new DelegatingRenderer($pimple['renderers']);
    };
  }
}