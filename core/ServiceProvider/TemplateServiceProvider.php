<?php


namespace LastCall\Patterns\Core\ServiceProvider;


use LastCall\Patterns\Core\Discovery\TemplateDiscovery;
use LastCall\Patterns\Core\Parser\HtmlTemplateParser;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;

class TemplateServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['template.finder'] = function() {
      return new Finder();
    };
    $pimple['template.parsers'] = function() use ($pimple) {
      return [
        new HtmlTemplateParser()
      ];
    };
    $pimple['discovery.template'] = function() use ($pimple) {
      return new TemplateDiscovery($pimple['template.finder'], $pimple['template.parsers']);
    };

  }
}