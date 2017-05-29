<?php

namespace LastCall\Mannequin\Drupal\Extension;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use LastCall\Mannequin\Drupal\Discovery\DrupalExtensionTwigDiscovery;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use Symfony\Component\HttpFoundation\Request;

class DrupalBootstrapExtension extends TwigExtension {

  public function __construct(array $config = []) {
    $config += [
      'drupal_root' => NULL,
      'extensions' => [],
    ];
    parent::__construct($config);

    $this['drupal'] = function() {
      chdir($this['drupal_root']);
      $autoloader = require $this['drupal_root'].'/autoload.php';
      require_once $this['drupal_root'] . '/core/includes/bootstrap.inc';

      $request = Request::create('/', 'GET', [], [], [], ['SCRIPT_NAME' => $this['drupal_root'] . '/index.php']);
      $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod', FALSE);
      Settings::initialize($this['drupal_root'], DrupalKernel::findSitePath($request), $autoloader);
      $kernel->boot();
      $kernel->preHandle($request);

      return $kernel->getContainer();
    };
    $this['twig'] = function() {
      return $this['drupal']->get('twig');
    };

    $this['discovery'] = function() {
      return new DrupalExtensionTwigDiscovery($this['drupal_root'], $this['extensions'], $this['twig']->getLoader(), $this->getConfig()->getDispatcher());
    };
  }

  public function getDiscoverers(): array {
    return [$this['discovery']];
  }

}