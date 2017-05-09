<?php


namespace LastCall\Patterns\Core\Extension;

use LastCall\Patterns\Core\ConfigInterface;

class AbstractExtension implements ExtensionInterface {

  protected $config;

  public function setConfig(ConfigInterface $config) {
    $this->config = $config;
  }

  protected function getConfig(): ConfigInterface {
    return $this->config;
  }

  public function getParsers(): array {
    return [];
  }

  public function getRenderers(): array {
    return [];
  }

  public function getDiscoverers(): array {
    return [];
  }

  public function getLabels(): array {
    return [];
  }

  public function getVariableFactories(): array {
    return [];
  }

}