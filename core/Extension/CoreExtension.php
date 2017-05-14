<?php


namespace LastCall\Patterns\Core\Extension;


use LastCall\Patterns\Core\Discovery\TemplateDiscovery;
use LastCall\Patterns\Core\Variable\PatternFactory;
use LastCall\Patterns\Core\Variable\ScalarFactory;
use LastCall\Patterns\Core\Variable\VariableSet;

class CoreExtension extends AbstractExtension {

  public function getVariableFactories(): array {
    $config = $this->getConfig();
    return [
      new ScalarFactory(),
      new PatternFactory(function($id, VariableSet $overrides) use($config) {
        $pattern = $config->getCollection()->get($id);
        return $config->getRenderer()->render($pattern, $overrides);
      })
    ];
  }
}