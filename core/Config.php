<?php

namespace LastCall\Patterns\Core;

use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\ServiceProvider\DiscoveryServiceProvider;
use LastCall\Patterns\Core\ServiceProvider\RendererServiceProvider;
use LastCall\Patterns\Core\ServiceProvider\TemplateServiceProvider;
use LastCall\Patterns\Core\ServiceProvider\VariableServiceProvider;
use Pimple\Container;

class Config extends Container {

  public static function create(array $values = []) {
    return new static($values);
  }

  public function __construct(array $values = []) {
    parent::__construct($values);
    $this['cache_dir'] = __DIR__.'/../cache';
    $this['labeller'] = function() {
      return new Labeller();
    };
    $this->register(new DiscoveryServiceProvider());
    $this->register(new RendererServiceProvider());
    $this->register(new VariableServiceProvider());
    $this->register(new TemplateServiceProvider());
  }

  /**
   * @return PatternCollection
   */
  public function getCollection() {
    return $this['discovery']->discover();
  }

  /**
   * @return \LastCall\Patterns\Core\Render\RendererInterface
   */
  public function getRenderer() {
    return $this['renderer'];
  }

  /**
   * @return \LastCall\Patterns\Core\Labeller
   */
  public function getLabeller() {
    return $this['labeller'];
  }

}