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

class TwigFileDiscovery implements DiscoveryInterface {
  use IdEncoder;

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

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->files as $fileInfo) {
      $patterns[] = $this->parseFile($fileInfo);
    }
    return new PatternCollection($patterns);
  }

  private function parseFile(SplFileInfo $fileInfo) {
    try {
      $source = $this->loader->getSourceContext($fileInfo->getRelativePathname());
    }
    catch(\Twig_Error_Loader $e) {
      throw new UnsupportedPatternException(sprintf('Unable to load %s', $fileInfo->getRelativePathname()), 0, $e);
    }
    $id = sprintf('%s://%s', $this->prefix, $fileInfo->getRelativePathname());
    $pattern = new TwigPattern($this->encodeId($id), [$id], $source);
    $pattern->addTag('format', 'twig');
    $this->dispatcher->dispatch(PatternEvents::DISCOVER, new PatternDiscoveryEvent($pattern));
    return $pattern;
  }
}