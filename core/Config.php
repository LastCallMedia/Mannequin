<?php

namespace LastCall\Patterns\Core;

use LastCall\Patterns\Core\Discovery\ChainDiscovery;
use LastCall\Patterns\Core\Extension\CoreExtension;
use LastCall\Patterns\Core\Extension\ExtensionInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Render\DelegatingRenderer;
use LastCall\Patterns\Core\Render\RendererInterface;
use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Core\Variable\VariableFactoryInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use Pimple\Container;
use Symfony\Component\Finder\Finder;

class Config extends Container implements ConfigInterface {

  public static function create(array $values = []): ConfigInterface {
    return new static($values);
  }

  public function __construct(array $values = []) {
    parent::__construct($values);
    $this['cache_dir'] = __DIR__.'/../cache';
    $this['labeller'] = function() {
      return new Labeller();
    };
    $this['finder'] = new Finder();
    $this['extensions'] = function() {
      return [];
    };
    $this['discovery'] = function() {
      $discoverers = [];
      foreach($this->getExtensions() as $extension) {
        $discoverers = array_merge($discoverers, $extension->getDiscoverers());
      }
      return new ChainDiscovery($discoverers);
    };
    $this['renderer'] = function() {
      $renderers = [];
      foreach($this->getExtensions() as $extension) {
        $renderers = array_merge($renderers, $extension->getRenderers());
      }
      return new DelegatingRenderer($renderers);
    };
    $this['variables'] = function() {
      return new VariableSet();
    };
    $this['variable.factory'] = function() {
      $factories = [];
      foreach($this->getExtensions() as $extension) {
        $factories = array_merge($factories, $extension->getVariableFactories());
      }
      return new VariableFactory([], $factories);
    };
    $this['collection'] = function() {
      return $this['discovery']->discover();
    };
    $this['styles'] = function() {
      return [];
    };
    $this['scripts'] = function() {
      return [];
    };

    $this->addExtension(new CoreExtension());
  }

  /**
   * @return PatternCollection
   */
  public function getCollection(): PatternCollection {
    return $this['collection'];
  }

  /**
   * {@inheritdoc}
   */
  public function addExtension(ExtensionInterface $extension): ConfigInterface {
    $this->extend('extensions', function(array $extensions) use ($extension) {
      $extension->setConfig($this);
      $extensions[] = $extension;
      return $extensions;
    });
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensions(): array {
    return $this['extensions'];
  }

  /**
   * {@inheritdoc}
   */
  public function getStyles(): array {
    return $this['styles'];
  }

  /**
   * {@inheritdoc}
   */
  public function addStyles(array $styles): ConfigInterface {
    $this->extend('styles', function(array $existing) use ($styles) {
      $existing = array_merge($existing, $styles);
      return $existing;
    });
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getScripts(): array {
    return $this['scripts'];
  }

  /**
   * {@inheritdoc}
   */
  public function addScripts(array $scripts): ConfigInterface {
    $this->extend('scripts', function(array $existing) use ($scripts) {
      $existing = array_merge($existing, $scripts);
      return $existing;
    });
    return $this;
  }

  public function getVariables(): VariableSet {
    return $this['variables'];
  }

  /**
   * @return \LastCall\Patterns\Core\Render\RendererInterface
   */
  public function getRenderer(): RendererInterface {
    return $this['renderer'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLabeller(): Labeller {
    return $this['labeller'];
  }

  public function getFinder(): Finder {
    return $this['finder'];
  }

  public function getVariableFactory(): VariableFactoryInterface {
    return $this['variable.factory'];
  }

  public function getCacheDir(): string {
    return $this['cache_dir'];
  }
}