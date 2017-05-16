<?php


namespace LastCall\Patterns\Html\Discovery;


use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Metadata\MetadataFactoryInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Html\Pattern\HtmlPattern;
use Symfony\Component\Finder\Finder;

class HtmlDiscovery implements DiscoveryInterface {

  private $finder;
  private $metadataParser;
  private $prefix;

  public function __construct(Finder $finder, MetadataFactoryInterface $metadataParser, $prefix = 'html://') {
    $this->finder = $finder;
    $this->metadataParser = $metadataParser;
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
    $pattern = new HtmlPattern($this->prefix.$fileInfo->getRelativePathname(), $fileInfo);

    if($this->metadataParser->hasMetadata($pattern)) {
      $metadata = $this->metadataParser->getMetadata($pattern);
      $pattern->setName($metadata['name']);
      $pattern->setVariables($metadata['variables']);
      $pattern->setTags($metadata['tags']);
    }
    return $pattern;
  }
}