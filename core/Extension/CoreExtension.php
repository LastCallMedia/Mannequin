<?php


namespace LastCall\Patterns\Core\Extension;


use LastCall\Patterns\Core\Discovery\TemplateDiscovery;
use LastCall\Patterns\Core\Variable\PatternFactory;
use LastCall\Patterns\Core\Variable\ScalarFactory;

class CoreExtension extends AbstractExtension {

  public function getDiscoverers(): array {
    $config = $this->getConfig();
    $finder = $config->getFinder();
    $parsers = [];
    /** @var \LastCall\Patterns\Core\Extension\ExtensionInterface $extension */
    foreach($config->getExtensions() as $extension) {
      $parsers = array_merge($parsers, $extension->getParsers());
    }
    return [new TemplateDiscovery($finder, $parsers)];
  }

  public function getVariableFactories(): array {
    $config = $this->getConfig();
    return [
      new ScalarFactory(),
      new PatternFactory(function($id) use($config) {
        $pattern = $config->getCollection()->get($id);
        return $config->getRenderer()->render($pattern);
      })
    ];
  }
}