<?php

namespace LastCall\Patterns\Core;

use LastCall\Patterns\Core\Discovery\ChainDiscovery;
use LastCall\Patterns\Core\Discovery\ExplicitDiscovery;
use LastCall\Patterns\Core\Discovery\TemplateDiscovery;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Render\DelegatingRenderer;
use LastCall\Patterns\Core\Render\HtmlRenderer;
use LastCall\Patterns\Core\Render\TemplatingRenderer;
use LastCall\Patterns\Core\ServiceProvider\TemplateServiceProvider;
use Pimple\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

class Config extends Container {

  public static function create(array $values = []) {
    return new static($values);
  }

  public function __construct(array $values = []) {
    parent::__construct($values);
    $this['discovery'] = function() {
      return new ChainDiscovery($this['discoverers']);
    };
    $this['discoverers'] = function() {
      return [];
    };
    $this['labeller'] = function() {
      return new Labeller();
    };
    $this['renderers'] = function() {
      return [];
    };
    $this['renderer'] = function() {
      return new DelegatingRenderer($this['renderers']);
    };
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