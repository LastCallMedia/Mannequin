<?php


namespace LastCall\Mannequin\Html\Extension;


use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Engine\HtmlEngine;
use Symfony\Component\Finder\Finder;

class HtmlExtension extends AbstractExtension {

  public function __construct(array $values = array()) {
    $values += [
      'finder' => function() {
        throw new \RuntimeException('Finder must be set on HtmlExtension.');
      }
    ];
    parent::__construct($values);
    $this['discovery'] = function() {
      return new HtmlDiscovery($this['finder']);
    };
    $this['renderer'] = function() {
      $config = $this->getConfig();
      return new HtmlEngine($config->getStyles(), $config->getScripts());
    };
  }

  public function getDiscoverers(): array {
    return [ $this['discovery'] ];
  }

  public function getRenderers(): array {
    return [ $this['renderer'] ];
  }
}