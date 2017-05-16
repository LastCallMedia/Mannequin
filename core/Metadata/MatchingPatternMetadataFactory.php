<?php


namespace LastCall\Patterns\Core\Metadata;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableSet;

class MatchingPatternMetadataFactory implements MetadataFactoryInterface {

  private $idPattern;
  private $tags;

  public function __construct(string $idPattern, array $tags) {
    $this->idPattern = $idPattern;
    $this->tags = $tags;
  }

  public function hasMetadata(PatternInterface $pattern): bool {
    return $this->matches($pattern);
  }

  public function getMetadata(PatternInterface $pattern): array {
    if($this->matches($pattern)) {
      return [
        'name' => '',
        'tags' => $this->tags,
        'variables' => new VariableSet()
      ];
    }
    throw new \InvalidArgumentException('Matches called with nonmatching pattern.');
  }

  protected function matches(PatternInterface $pattern) {
    return preg_match($this->idPattern, $pattern->getId());
  }
}