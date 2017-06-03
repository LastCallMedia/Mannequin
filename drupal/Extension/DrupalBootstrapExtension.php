<?php

namespace LastCall\Mannequin\Drupal\Extension;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use LastCall\Mannequin\Drupal\Discovery\DrupalExtensionTwigDiscovery;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use LastCall\Mannequin\Twig\TemplateFilenameIterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class DrupalBootstrapExtension extends TwigExtension {

  public function __construct(array $config = []) {
    $config += [
      'drupal_root' => NULL,
      'extensions' => [],
      'prefix' => 'drupal',
      'drupal' => function() {
        return $this->bootDrupal();
      },
      'finder' => function() {
        $finder = Finder::create()
          ->name('*.html.twig')
          ->files();
        foreach($this['extension_paths'] as $extension_path) {
          $finder->in($extension_path);
        }
        return $finder;
      },
      'names' => function() {
        $loader = $this['drupal']->get('twig.loader.filesystem');
        $iterator = new TemplateFilenameIterator($this['finder']);
        foreach($loader->getNamespaces() as $namespace) {
          foreach($loader->getPaths($namespace) as $path) {
            $iterator->addPath(sprintf('%s/%s', $this['drupal_root'], $path), $namespace);
          }
        }
        return $iterator;
      }
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
    $this['extension_paths'] = function() {
      $paths = [];
      foreach($this['extensions'] as $extension) {
        $path = drupal_get_path('theme', $extension) ?: drupal_get_path('module', $extension);
        $paths[] = realpath(sprintf('%s/%s/templates', $this['_drupal_root'], $path));
      }
      return $paths;
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