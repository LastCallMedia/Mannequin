<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Parser\HtmlTemplateParser;
use LastCall\Patterns\Core\Render\HtmlRenderer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HtmlServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['renderer.html'] = function() use ($pimple) {
      return new HtmlRenderer($pimple['styles'], $pimple['scripts']);
    };
    $pimple['parser.html'] = function() use ($pimple) {
      return new HtmlTemplateParser();
    };
    if(isset($pimple['renderers'])) {
      $pimple->extend('renderers', function(array $renderers) use($pimple) {
        $renderers[] = $pimple['renderer.html'];
        return $renderers;
      });
    }
    if(isset($pimple['parsers'])) {
      $pimple->extend('parsers', function(array $parsers) use ($pimple) {
        $parsers[] = $pimple['parser.html'];
        return $parsers;
      });
    }
  }
}