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
      'drupal' => function() {
        return $this->bootDrupal();
      },
    ];
    parent::__construct($config);

    $this['_drupal_root'] = function() {
      if(is_dir($this['drupal_root'])) {
        return $this['drupal_root'];
      }
      throw new \InvalidArgumentException(sprintf('Invalid Drupal Root: %s', $this['drupal_root']));
    };
    $this['twig'] = function() {
      return $this['drupal']->get('twig');
    };
    $this['discovery'] = function() {
      return new DrupalExtensionTwigDiscovery($this['_drupal_root'], $this['extensions'], $this['twig']->getLoader());
    };
  }

  protected function bootDrupal() {
    $drupal_root = $this['_drupal_root'];
    chdir($drupal_root);
    $autoloader = require $drupal_root.'/autoload.php';
    require_once $drupal_root . '/core/includes/bootstrap.inc';

    $request = Request::create('/', 'GET', [], [], [], ['SCRIPT_NAME' => $drupal_root . '/index.php']);
    $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod', FALSE);
    Settings::initialize($drupal_root, DrupalKernel::findSitePath($request), $autoloader);
    $kernel->boot();
    $kernel->preHandle($request);

    return $kernel->getContainer();
  }

  public function getDiscoverers(): array {
    return [$this['discovery']];
  }

}