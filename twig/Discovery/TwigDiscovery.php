<?php


namespace LastCall\Mannequin\Twig\Discovery;


use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

/**
 * This class converts an iterable object of template names into TwigPattern
 * objects by using the Twig Loader.
 */
class TwigDiscovery implements DiscoveryInterface {
  use IdEncoder;

  private $loader;
  private $names;
  private $prefix;

  public function __construct(\Twig_LoaderInterface $loader, $names, $prefix = 'twig') {
    $this->loader = $loader;
    if(!is_array($names) && !$names instanceof \Traversable) {
      throw new \InvalidArgumentException('$names must be an array or a \Traversable object.');
    }
    $this->names = $names;
    $this->prefix = $prefix;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->names as $name) {
      try {
        $source = $this->loader->getSourceContext($name);
        $name = $this->prefixId($name);
        $pattern = new TwigPattern($this->encodeId($name), [$name], $source);
        $pattern->addTag('format', 'twig');
        $patterns[] = $pattern;
      }
      catch(\Twig_Error_Loader $e) {
        throw new UnsupportedPatternException(sprintf('Unable to load %s', $name), 0, $e);
      }
    }
    return new PatternCollection($patterns);
  }

  private function prefixId($id) {
    return sprintf('%s://%s', $this->prefix, $id);
  }
}