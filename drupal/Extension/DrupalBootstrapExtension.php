<?php


namespace LastCall\Patterns\Drupal\Extension;


use LastCall\Patterns\Twig\Extension\TwigExtension;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\Request;

class DrupalBootstrapExtension extends TwigExtension {

  public function __construct(array $config = []) {
    $config += [
      'drupal_root' => NULL,
    ];
    parent::__construct($config);

    $this['drupal'] = function() {
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
  }
}