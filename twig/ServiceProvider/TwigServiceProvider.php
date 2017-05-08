<?php


namespace LastCall\Patterns\Twig\ServiceProvider;


use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Render\TwigRenderer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TwigServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['twig.paths'] = function() {
      return [];
    };
    $pimple['twig.loader.filesystem'] = function() use ($pimple) {
      return new \Twig_Loader_Filesystem($pimple['twig.paths']);
    };
    $pimple['twig.loaders'] = function() use ($pimple) {
      return [$pimple['twig.loader.filesystem']];
    };
    $pimple['twig.loader'] = function() use ($pimple) {
      return new \Twig_Loader_Chain($pimple['twig.loaders']);
    };
    $pimple['twig'] = function() use ($pimple) {
      return new \Twig_Environment($pimple['twig.loader'], [
        'cache' => $pimple['cache_dir'].'/twig',
        'auto_reload' => TRUE,
      ]);
    };

    $pimple['template.parser.twig'] = function() use($pimple) {
      return new TwigParser($pimple['twig'], $pimple['variable.factory']);
    };
    $pimple['renderer.twig'] = function() use ($pimple) {
      return new TwigRenderer($pimple['twig'], $pimple['variables.global'], $pimple['styles'], $pimple['scripts']);
    };
    if(isset($pimple['template.parsers'])) {
      $pimple->extend('template.parsers', function(array $parsers) use ($pimple) {
        $parsers[] = $pimple['template.parser.twig'];
        return $parsers;
      });
    }
    if(isset($pimple['renderers'])) {
      $pimple->extend('renderers', function(array $renderers) use ($pimple) {
        $renderers[] = $pimple['renderer.twig'];
        return $renderers;
      });
    }

  }
}