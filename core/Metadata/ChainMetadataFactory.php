<?php


namespace LastCall\Mannequin\Core\Metadata;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;

class ChainMetadataFactory implements MetadataFactoryInterface {

  private $factories = [];

  /**
   * ChainMetadataFactory constructor.
   *
   * @param MetadataFactoryInterface[] $factories
   */
  public function __construct(array $factories = []) {
    foreach($factories as $factory) {
      if(!$factory instanceof MetadataFactoryInterface) {
        throw new \InvalidArgumentException('Factory must be an instance of MetadataFactoryInterface');
      }
      $this->factories[] = $factory;
    }
  }

  public function hasMetadata(PatternInterface $pattern): bool {
    foreach($this->factories as $factory) {
      if($factory->hasMetadata($pattern)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function getMetadata(PatternInterface $pattern): array {
    $sets = [];
    foreach($this->factories as $factory) {
      if($factory->hasMetadata($pattern)) {
        $sets[] = $factory->getMetadata($pattern);
      }
    }
    return $this->resolveSets($sets);
  }

  private function resolveSets(array $sets) {
    $metadata = [
      'name' => '',
      'description' => '',
      'tags' => [],
      'variables' => new VariableSet(),
    ];
    foreach($sets as $set) {
      if(!empty($set['name'])) {
        $metadata['name'] = $set['name'];
      }
      $metadata['description'] = $set['description'] ?: $metadata['description'];
      $metadata['tags'] = $set['tags'] + $metadata['tags'];
      $metadata['variables'] = $metadata['variables']->merge($set['variables']);
    }
    return $metadata;
  }
}