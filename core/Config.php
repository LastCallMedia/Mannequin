<?php

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Render\DelegatingRenderer;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Variable\VariableFactory;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;
use Pimple\Container;

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
    $this['assets'] = function() {
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

  public function addAssetMapping($url, $path): ConfigInterface {
    if(!is_string($url) || strlen($url) === 0 || strpos($url, '/') === 0) {
      throw new \InvalidArgumentException('URL path specified for %s is invalid.  It should be a relative URL.');
    }
    if(!file_exists($path)) {
      throw new \InvalidArgumentException('Path specified for asset url %s is invalid.', $url);
    }
    $this->extend('assets', function(array $existing) use ($url, $path) {
      $existing[$url] = $path;
      return $existing;
    });
    return $this;
  }

  public function getAssetMappings(): array {
    return $this['assets'];
  }

  public function getVariables(): VariableSet {
    return $this['variables'];
  }

  /**
   * @return \LastCall\Mannequin\Core\Render\RendererInterface
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

  public function getVariableFactory(): VariableFactoryInterface {
    return $this['variable.factory'];
  }

  public function getCacheDir(): string {
    return $this['cache_dir'];
  }
}