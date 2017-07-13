<?php

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Manifester {

  private $generator;

  public function __construct(UrlGeneratorInterface $generator) {
    $this->generator = $generator;
  }

  public function generate(PatternCollection $collection) {
    $manifest = [];
    $generator = $this->generator;
    foreach($collection as $pattern) {
      $id = $pattern->getId();
      $manifest['patterns'][] = [
        'id' => $id,
        'source' => $generator->generate('pattern_render_source_raw', ['pattern' => $id]),
        'name' => $pattern->getName(),
        'description' => $pattern->getDescription(),
        'tags' => $pattern->getTags(),
        'sets' => $this->generateSets($pattern),
        'used' => $this->generateUsed($pattern),
        'aliases' => $pattern->getAliases(),
        'format' => $pattern->getRawFormat(),
      ];
    }
    return $manifest;
  }

  private function generateSets(PatternInterface $pattern) {
    $sets = [];
    $generator = $this->generator;
    foreach($pattern->getVariableSets() as $id => $set) {
      $sets[] = [
        'id' => $id,
        'name' => $set->getName(),
        'description' => $set->getDescription(),
        'source' => $generator->generate('pattern_render_raw', ['pattern' => $pattern->getId(), 'set' => $id], UrlGeneratorInterface::RELATIVE_PATH),
        'rendered' => $generator->generate('pattern_render', ['pattern' => $pattern->getId(), 'set' => $id], UrlGeneratorInterface::RELATIVE_PATH),
      ];
    }
    return $sets;
  }

  private function generateUsed(PatternInterface $pattern) {
    return array_map(function(PatternInterface $used) {
      return $used->getId();
    }, $pattern->getUsedPatterns());
  }
}