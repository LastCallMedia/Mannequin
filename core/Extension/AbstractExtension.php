<?php


namespace LastCall\Patterns\Core\Extension;

use LastCall\Patterns\Core\ConfigInterface;
use Pimple\Container;

class AbstractExtension extends Container implements ExtensionInterface {

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