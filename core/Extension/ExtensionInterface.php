<?php

namespace LastCall\Patterns\Core\Extension;

use LastCall\Patterns\Core\ConfigInterface;

interface ExtensionInterface {

  /**
   * Set the configuration instance.
   *
   * This method will be called when extensions are first called for.
   * The container will be available before calling any of the get* mehtods,
   * but not in the constructor.
   *
   * @param \LastCall\Patterns\Core\ConfigInterface $container
   *
   * @return void
   */
  public function setConfig(ConfigInterface $container);

  /**
   * Get the pattern discoverers provided by this extension.
   *
   * @return \LastCall\Patterns\Core\Discovery\DiscoveryInterface[]
   */
  public function getDiscoverers(): array;

  /**
   * Get the pattern renderers provided by this extension.
   *
   * @return \LastCall\Patterns\Core\Render\RendererInterface[]
   */
  public function getRenderers(): array;

  /**
   * Get the variable factories provided by this extension.
   *
   * @return \LastCall\Patterns\Core\Variable\VariableFactoryInterface[]
   */
  public function getVariableFactories(): array;

}
