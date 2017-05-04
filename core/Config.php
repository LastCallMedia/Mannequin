<?php

namespace LastCall\Patterns\Core;

use LastCall\Patterns\Core\Discovery\ExplicitDiscovery;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Render\DelegatingRenderer;
use LastCall\Patterns\Core\Render\TemplatingRenderer;
use Pimple\Container;
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
    $this['templating'] = function() {
      return new DelegatingEngine($this['engines']);
    };
    $this['engines'] = function() {
      return [
        $this['engine.php']
      ];
    };
    $this['engine.php.paths'] = function() {
      return [];
    };
    $this['engine.php'] = function() {
      return new PhpEngine(new TemplateNameParser(), new FilesystemLoader($this['engine.php.paths']));
    };
    $this['discovery'] = function() {
      return new ExplicitDiscovery(new PatternCollection($this['patterns']));
    };
    $this['labeller'] = function() {
      return new Labeller();
    };
    $this['patterns'] = [];
    $this['renderer'] = function() {
      return new DelegatingRenderer([
        new TemplatingRenderer($this['templating'], $this['styles'], $this['scripts'])
      ]);
    };
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