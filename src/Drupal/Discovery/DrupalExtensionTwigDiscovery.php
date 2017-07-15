<?php


namespace LastCall\Mannequin\Drupal\Discovery;

use LastCall\Mannequin\Twig\Discovery\AbstractTwigDiscovery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class DrupalExtensionTwigDiscovery extends AbstractTwigDiscovery {

  private $drupalRoot;
  private $extensions = [];
  private $loader;
  private $prefix;

  public function __construct(string $drupal_root, array $extensions, \Twig_LoaderInterface $loader, string $prefix = 'drupal') {
    $this->drupalRoot = $drupal_root;
    $this->extensions = $extensions;
    $this->loader = $loader;
    $this->prefix = $prefix;
  }

  protected function getLoader(): \Twig_LoaderInterface {
    return $this->loader;
  }

  protected function getPrefix(): string {
    return $this->prefix;
  }

  protected function getNames(): array {
    $names = [];
    foreach($this->extensions as $extension) {
      $names = array_merge($names, $this->getExtensionNames($extension));
    }
    return $names;
  }

  private function getExtensionNames($extension): array {
    $path = drupal_get_path('theme', $extension) ?: drupal_get_path('module', $extension);
    if(!$path) {
      throw new \RuntimeException(sprintf('Unable to determine a path for %s', $extension));
    }

    $finder = Finder::create()
      ->files()
      ->name('*.html.twig')
      ->in(sprintf('%s/%s/templates', $this->drupalRoot, $path));

    $names = [];
    foreach($finder as $fileInfo) {
      $names[] = sprintf('@%s/%s', $extension, $fileInfo->getRelativePathname());
    }
    return $names;
  }
}