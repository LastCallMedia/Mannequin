<?php


namespace LastCall\Mannequin\Core;


use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;
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
   * @param string[] $styles
   */
  public function addStyles(array $styles): ConfigInterface;

  /**
   * @return string[]
   */
  public function getScripts(): array;

  /**
   * @param string[] $scripts
   */
  public function addScripts(array $scripts): ConfigInterface;

  public function addAssetMapping($url, $path): ConfigInterface;

  public function getAssetMappings(): array;

  public function getVariables(): VariableSet;

  public function getRenderer(): RendererInterface;

  public function getLabeller(): Labeller;

  public function getVariableFactory(): VariableFactoryInterface;

  public function getCacheDir(): string;

  public function getDispatcher(): EventDispatcherInterface;
}