<?php


namespace LastCall\Mannequin\Core\Extension;

use LastCall\Mannequin\Core\ConfigInterface;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AbstractExtension extends Container implements ExtensionInterface {

  protected $config;

  /**
   * {@inheritdoc}
   */
  public function setConfig(ConfigInterface $config) {
    $this->config = $config;
  }

  /**
   * Get the configuration instance.
   *
   * Config will be set at the time the container is instantiated, but will
   * not be available in the constructor.
   *
   * @return ConfigInterface|null
   */
  protected function getConfig(): ConfigInterface {
    return $this->config;
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderers(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDiscoverers(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getVariableResolvers(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function attachToDispatcher(EventDispatcherInterface $dispatcher) {

  }

}