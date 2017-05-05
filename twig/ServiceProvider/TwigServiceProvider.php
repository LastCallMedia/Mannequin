<?php


namespace LastCall\Patterns\Twig\ServiceProvider;


use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Render\TwigRenderer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TwigServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['twig'] = function() {
      return new \Twig_Environment(new \Twig_Loader_Filesystem([__DIR__.'/../'], '/'));
    };

    $pimple['template.parser.twig'] = function() {
      return new TwigParser();
    };
    $pimple['renderer.twig'] = function() use ($pimple) {
      return new TwigRenderer($pimple['twig'], $pimple['styles'], $pimple['scripts']);
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