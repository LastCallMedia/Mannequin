<?php


namespace LastCall\Mannequin\Twig\Discovery;

use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;

abstract class AbstractTwigDiscovery implements DiscoveryInterface {
  use IdEncoder;

  abstract protected function getLoader(): \Twig_LoaderInterface;

  abstract protected function getNames(): array;

  abstract protected function getPrefix(): string;

  public function discover(): PatternCollection {
    $patterns = array_map([$this, 'createPattern'], $this->getNames());
    return new PatternCollection($patterns);
  }

  private function createPattern($name): TwigPattern {
    try {
      $source = $this->getLoader()->getSourceContext($name);
    }
    catch (\Twig_Error_Loader $e) {
      throw new UnsupportedPatternException(sprintf('Unable to load %s', $name), 0, $e);
    }
    $id = sprintf('%s://%s', $this->getPrefix(), $name);
    $pattern = new TwigPattern($this->encodeId($id), [$id], $source);
    $pattern->addTag('format', 'twig');
    return $pattern;
  }
}