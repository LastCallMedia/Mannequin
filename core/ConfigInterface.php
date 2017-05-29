<?php


namespace LastCall\Mannequin\Core;


use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ConfigInterface {
  public function getCollection(): PatternCollection;
  public function addExtension(ExtensionInterface $extension): ConfigInterface;

  /**
   * @return ExtensionInterface[]
   */
  public function getExtensions(): array;

  /**
   * @return string[]
   */
  public function getStyles(): array;

  /**
   * @return string[]
   */
  public function getScripts(): array;

  public function addAssetMapping($url, $path): ConfigInterface;

  public function getAssetMappings(): array;

  public function getRenderer(): EngineInterface;

  public function getLabeller(): Labeller;

  public function getVariableResolver(): SetResolver;

  public function getCacheDir(): string;

  public function getDispatcher(): EventDispatcherInterface;
}