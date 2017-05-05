<?php


namespace LastCall\Patterns\Core\Discovery;


use LastCall\Patterns\Core\Parser\TemplateFileParserInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use Symfony\Component\Finder\Finder;

class TemplateDiscovery implements DiscoveryInterface {

  private $finder;
  /** @var TemplateFileParserInterface[] */
  private $parsers = [];

  /**
   * TemplateDiscovery constructor.
   *
   * @param \Symfony\Component\Finder\Finder $finder
   * @param TemplateFileParserInterface[]    $parsers
   */
  public function __construct(Finder $finder, array $parsers = []) {
    $this->finder = $finder;
    foreach($parsers as $parser) {
      if(!$parser instanceof TemplateFileParserInterface) {
        throw new \InvalidArgumentException(sprintf('Template file parsers must implement %s', TemplateFileParserInterface::class));
      }
      $this->parsers[] = $parser;
    }
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->finder as $fileInfo) {
      if($parser = $this->findParserForFile($fileInfo)) {
        $patterns[] = $parser->parse($fileInfo);
      }
    }
    return new PatternCollection($patterns);
  }

  private function findParserForFile(\SplFileInfo $fileInfo) {
    foreach($this->parsers as $parser) {
      if($parser->supports($fileInfo)) {
        return $parser;
      }
    }
  }

}