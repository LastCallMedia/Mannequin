<?php


namespace LastCall\Patterns\Core;


use LastCall\Patterns\Core\Extension\ExtensionInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Render\RendererInterface;
use LastCall\Patterns\Core\Variable\VariableFactoryInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use Symfony\Component\Finder\Finder;

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

  public function getVariables(): VariableSet;

  public function getRenderer(): RendererInterface;

  public function getLabeller(): Labeller;

  public function getFinder(): Finder;

  public function getVariableFactory(): VariableFactoryInterface;

  public function getCacheDir(): string;
}