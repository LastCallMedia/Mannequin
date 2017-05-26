<?php

namespace LastCall\Mannequin\Core\Extension;

use LastCall\Mannequin\Core\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ExtensionInterface {

  /**
   * Set the configuration instance.
   *
   * This method will be called when extensions are first called for.
   * The container will be available before calling any of the get* mehtods,
   * but not in the constructor.
   *
   * @param \LastCall\Mannequin\Core\ConfigInterface $container
   *
   * @return void
   */
  public function setConfig(ConfigInterface $container);

  /**
   * Get the pattern discoverers provided by this extension.
   *
   * @return \LastCall\Mannequin\Core\Discovery\DiscoveryInterface[]
   */
  public function getDiscoverers(): array;

  /**
   * Get the pattern renderers provided by this extension.
   *
   * @return \LastCall\Mannequin\Core\Render\RendererInterface[]
   */
  public function getRenderers(): array;

  /**
   * Get the variable factories provided by this extension.
   *
   * @return \LastCall\Mannequin\Core\Variable\VariableFactoryInterface[]
   */
  public function getVariableFactories(): array;

  /**
   * Attach an extension's listeners/subscribers to a dispatcher.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *
   * @return void
   */
  public function attachToDispatcher(EventDispatcherInterface $dispatcher);

}
