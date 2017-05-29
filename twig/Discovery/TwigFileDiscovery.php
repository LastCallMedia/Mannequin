<?php


namespace LastCall\Mannequin\Twig\Discovery;


use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigFileDiscovery extends AbstractTwigDiscovery {

  /**
   * @var \Twig_LoaderInterface|\Twig_SourceContextLoaderInterface|\Twig_ExistsLoaderInterface
   */
  private $loader;
  private $files;
  private $prefix = 'twig';
  private $dispatcher;

  public function __construct(\Twig_LoaderInterface $loader, \Traversable $files, EventDispatcherInterface $dispatcher) {
    if(!$loader instanceof \Twig_SourceContextLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement \Twig_SourceContextLoaderInterface');
    }
    if(!$loader instanceof \Twig_ExistsLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_ExistsLoaderInterface');
    }
    $this->loader = $loader;
    $this->files = $files;
    $this->dispatcher = $dispatcher;
  }

  protected function getNames(): array {
    $names = [];
    foreach($this->files as $file) {
      $names[] = $file->getRelativePathname();
    }
    return $names;
  }

  protected function getDispatcher(): EventDispatcherInterface {
    return $this->dispatcher;
  }

  protected function getLoader(): \Twig_LoaderInterface {
    return $this->loader;
  }

  protected function getPrefix(): string {
    return $this->prefix;
  }
}