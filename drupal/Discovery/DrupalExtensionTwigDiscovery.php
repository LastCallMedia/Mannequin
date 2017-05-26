<?php


namespace LastCall\Mannequin\Drupal\Discovery;


use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Drupal\Pattern\DrupalTwigPattern;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DrupalExtensionTwigDiscovery implements DiscoveryInterface {

  use IdEncoder;

  private $drupalRoot;
  private $extensions = [];
  private $container;
  private $loader;
  private $prefix = 'drupal';

  public function __construct(string $drupal_root, array $extensions, ContainerInterface $container, \Twig_LoaderInterface $loader, EventDispatcherInterface $dispatcher) {
    $this->drupalRoot = $drupal_root;
    $this->extensions = $extensions;
    $this->container = $container;
    $this->loader = $loader;
    $this->dispatcher = $dispatcher;
  }

  public function discover(): PatternCollection {
    $patterns = [];

    foreach($this->extensions as $extension) {
      $patterns = array_merge($patterns, $this->discoverExtensionPatterns($extension));
    }
    return new PatternCollection($patterns);
  }

  private function discoverExtensionPatterns($extension): array {
    $patterns = [];
    $path = drupal_get_path('theme', $extension) ?: drupal_get_path('module', $extension);
    if(!$path) {
      return $patterns;
    }

    $finder = Finder::create()
      ->files()
      ->name('*.html.twig')
      ->in(sprintf('%s/%s/templates', $this->drupalRoot, $path));
    foreach($finder as $fileInfo) {
      if($pattern = $this->parseFile($extension, $fileInfo)) {
        $patterns[] = $pattern;
      }
    }
    return $patterns;
  }

  private function parseFile(string $extension, SplFileInfo $fileInfo) {
    $twig_path = sprintf('@%s/%s', $extension, $fileInfo->getRelativePathname());
    if($this->loader->exists($twig_path)) {
      $id = sprintf('drupal:%s', $twig_path);
      $source = $this->loader->getSourceContext($twig_path);
      $pattern = new DrupalTwigPattern($this->encodeId($id), [$id], $source);
      $this->dispatcher->dispatch(PatternEvents::DISCOVER, new PatternDiscoveryEvent($pattern));
      return $pattern;
    }
  }
}