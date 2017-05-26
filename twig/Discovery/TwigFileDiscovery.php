<?php


namespace LastCall\Mannequin\Twig\Discovery;


use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigFileDiscovery implements DiscoveryInterface {
  use IdEncoder;

  /**
   * @var \Twig_LoaderInterface|\Twig_SourceContextLoaderInterface|\Twig_ExistsLoaderInterface
   */
  private $loader;
  private $finder;
  private $prefix = 'twig';
  private $dispatcher;

  public function __construct(\Twig_LoaderInterface $loader, Finder $finder, EventDispatcherInterface $dispatcher) {
    if(!$loader instanceof \Twig_SourceContextLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_SourceContextLoaderInterface');
    }
    if(!$loader instanceof \Twig_ExistsLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_ExistsLoaderInterface');
    }
    $this->loader = $loader;
    $this->finder = $finder;
    $this->dispatcher = $dispatcher;
  }

  public function setPrefix(string $prefix) {
    $this->prefix = $prefix;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->finder->files() as $fileInfo) {
      if($pattern = $this->parseFile($fileInfo)) {
        $patterns[] = $pattern;
      }
    }
    return new PatternCollection($patterns);
  }

  private function parseFile(SplFileInfo $fileInfo) {
    if($this->loader->exists($fileInfo->getRelativePathname())) {
      $id = sprintf('%s://%s', $this->prefix, $fileInfo->getRelativePathname());
      $source = $this->loader->getSourceContext($fileInfo->getRelativePathname());

      $pattern = new TwigPattern($this->encodeId($id), [$id], $source);
      $pattern->addTag('format', 'twig');
      $this->dispatcher->dispatch(PatternEvents::DISCOVER, new PatternDiscoveryEvent($pattern));
      return $pattern;
    }
  }
}