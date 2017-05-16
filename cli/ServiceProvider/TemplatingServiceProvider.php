<?php

namespace LastCall\Mannequin\Cli\ServiceProvider;

use LastCall\Mannequin\Cli\Templating\PresetEngineTemplateNameParser;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;

class TemplatingServiceProvider implements ServiceProviderInterface {

  public function register(Container $pimple) {
    $pimple['templating.globals'] = function() {
      return [];
    };
    $pimple['templating.helpers'] = function() {
      return [];
    };
    $pimple['templating.loader'] = function() use ($pimple) {
      $directories = isset($pimple['templating.directories'])
        ? $pimple['templating.directories']
        : [];
      return new FilesystemLoader($directories);
    };
    $pimple['templating.engines.php'] = function() use ($pimple) {
      $engine = new PhpEngine(new PresetEngineTemplateNameParser('php', 'html.php'), $pimple['templating.loader']);
      $globals = $pimple['templating.globals'];
      if(!is_array($globals)) {
        throw new \RuntimeException('Invalid templating.globals - must be an array.');
      }
      foreach($globals as $key => $value) {
        $engine->addGlobal($key, $value);
      }
      $engine->addHelpers($pimple['templating.helpers']);
      return $engine;
    };
    $pimple['templating.engines'] = function() use ($pimple) {
      return [$pimple['templating.engines.php']];
    };
    $pimple['templating'] = function() use ($pimple) {
      return new DelegatingEngine($pimple['templating.engines']);
    };
  }
}