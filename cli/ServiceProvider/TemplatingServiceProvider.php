<?php


namespace LastCall\Patterns\Cli\ServiceProvider;


use LastCall\Patterns\Cli\Templating\PresetEngineTemplateNameParser;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;

class TemplatingServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['templating.loader'] = function() use ($pimple) {
      $directories = isset($pimple['templating.directories'])
        ? $pimple['templating.directories']
        : [];
      return new FilesystemLoader($directories);
    };
    $pimple['templating.engines'] = function() use ($pimple) {
      return [
        new PhpEngine(new PresetEngineTemplateNameParser('php', 'html.php'), $pimple['templating.loader'])
      ];
    };
    $pimple['templating'] = function() use ($pimple) {
      return new DelegatingEngine($pimple['templating.engines']);
    };
  }
}