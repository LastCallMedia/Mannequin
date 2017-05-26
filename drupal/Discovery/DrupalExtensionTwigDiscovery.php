<?php


namespace LastCall\Mannequin\Drupal\Discovery;


use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Drupal\Pattern\DrupalTwigPattern;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class DrupalExtensionTwigDiscovery implements DiscoveryInterface {

  use IdEncoder;

  public function __construct(string $drupal_root, array $extensions, ContainerInterface $container, \Twig_LoaderInterface $loader) {
    $this->drupalRoot = $drupal_root;
    $this->extensions = $extensions;
    $this->container = $container;
    $this->loader = $loader;
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
      $twig_path = sprintf('@%s/%s', $extension, $fileInfo->getRelativePathname());
      if($this->loader->exists($twig_path)) {
        $id = $this->encodeId(sprintf('drupal:%s', $twig_path));
        $source = $this->loader->getSourceContext($twig_path);
        $patterns[] = new DrupalTwigPattern($id, $source);
      }
    }
    return $patterns;
  }
}