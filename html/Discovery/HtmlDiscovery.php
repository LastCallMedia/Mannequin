<?php


namespace LastCall\Patterns\Html\Discovery;


use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Html\Pattern\HtmlPattern;
use Symfony\Component\Finder\Finder;

class HtmlDiscovery implements DiscoveryInterface {

  public function __construct(Finder $finder) {
    $this->finder = $finder;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->finder->files() as $fileInfo) {
      $patterns[] = $this->parsePattern($fileInfo);
    }
    return new PatternCollection($patterns);
  }

  protected function parsePattern(\SplFileInfo $fileInfo) {
    $basename = $fileInfo->getBasename('.'.$fileInfo->getExtension());
    $pattern = new HtmlPattern($basename, ucfirst($basename), $fileInfo);
    $pattern->addTag('renderer', 'HTML');
    return $pattern;
  }
}