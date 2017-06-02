<?php

namespace LastCall\Mannequin\Twig\Discovery;

class TwigFileDiscovery extends AbstractTwigDiscovery {

  /**
   * @var \Twig_LoaderInterface|\Twig_SourceContextLoaderInterface|\Twig_ExistsLoaderInterface
   */
  private $loader;
  private $files;
  private $prefix;

  public function __construct(\Twig_LoaderInterface $loader, \Traversable $files, string $prefix = 'twig') {
    if(!$loader instanceof \Twig_SourceContextLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement \Twig_SourceContextLoaderInterface');
    }
    if(!$loader instanceof \Twig_ExistsLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_ExistsLoaderInterface');
    }
    $this->loader = $loader;
    $this->files = $files;
    $this->prefix = $prefix;
  }

  protected function getNames(): array {
    $names = [];
    foreach($this->files as $file) {
      $names[] = $file->getRelativePathname();
    }
    return $names;
  }

  protected function getLoader(): \Twig_LoaderInterface {
    return $this->loader;
  }

  protected function getPrefix(): string {
    return $this->prefix;
  }
}