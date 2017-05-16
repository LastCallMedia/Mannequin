<?php


namespace LastCall\Mannequin\Core\Extension;


use LastCall\Mannequin\Core\Discovery\TemplateDiscovery;
use LastCall\Mannequin\Core\Variable\PatternFactory;
use LastCall\Mannequin\Core\Variable\ScalarFactory;
use LastCall\Mannequin\Core\Variable\VariableSet;

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