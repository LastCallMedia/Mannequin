<?php


namespace LastCall\Patterns\Core;


use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;

class Labeller {

  private $collectionLabels = [
    PatternCollection::ROOT_COLLECTION => 'All Patterns',
  ];
  private $tagLabels = [];

  public function getCollectionLabel(PatternCollection $collection) {
    $id = $collection->getId();
    if(isset($this->collectionLabels[$id])) {
      return $this->collectionLabels[$id];
    }
    elseif(preg_match('/tag:(.*):(.*)/', $id, $matches)) {
      return $this->pluralize($this->getTagLabel($matches[1], $matches[2]));
    }
    return $id;
  }

  public function getPatternLabel(PatternInterface $pattern) {
    return $pattern->getName();
  }

  public function getTagLabel($type, $value) {
    if(isset($this->tagLabels[$type]) && isset($this->tagLabels[$type][$value])) {
      return $this->tagLabels[$type][$value];
    }
    return ucfirst($value);
  }

  private function pluralize($word) {
    return $word.'s';
  }
}