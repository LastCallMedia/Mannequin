<?php


namespace LastCall\Mannequin\Html\Discovery;


use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use Symfony\Component\Finder\Finder;

class HtmlDiscovery implements DiscoveryInterface {
  use IdEncoder;

  private $finder;
  private $prefix;

  public function __construct(Finder $finder, $prefix = 'html') {
    $this->finder = $finder;
    $this->prefix = $prefix;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->finder as $fileInfo) {
      $patterns[] = $this->parsePattern($fileInfo);
    }
    return new PatternCollection($patterns);
  }

  protected function parsePattern(\SplFileInfo $fileInfo) {
    $id = sprintf('%s://%s', $this->prefix, $fileInfo->getRelativePathname());
    $pattern = new HtmlPattern($this->encodeId($id), [$id], $fileInfo);
    $pattern->addTag('format', 'html');
    return $pattern;
  }
}