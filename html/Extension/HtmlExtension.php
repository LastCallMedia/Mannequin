<?php


namespace LastCall\Mannequin\Html\Extension;


use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Engine\HtmlEngine;
use Symfony\Component\Finder\Finder;

class HtmlExtension extends AbstractExtension {

  public function __construct(array $values = array()) {
    parent::__construct($values);
    $this['finder'] = function() {
      $finder = new Finder();
      $finder->files();
      $finder->name('*.html');
      return $finder;
    };
    $this['discovery'] = function() {
      return new HtmlDiscovery($this['finder'], $this->getConfig()->getDispatcher());
    };
    $this['renderer'] = function() {
      $config = $this->getConfig();
      return new HtmlEngine($config->getStyles(), $config->getScripts());
    };
  }

  public function in($dirs) {
    return $this->extend('finder', function(Finder $finder) use($dirs) {
      $finder->in($dirs);
      return $finder;
    });
    return $this;
  }

  public function getDiscoverers(): array {
    return [ $this['discovery'] ];
  }

  public function getRenderers(): array {
    return [ $this['renderer'] ];
  }
}