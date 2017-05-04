<?php


namespace LastCall\Patterns\Core;


use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;

class Labeller {

  public function getCollectionLabel(PatternCollection $collection) {
    $id = $collection->getId();
    if($id === PatternCollection::ROOT_COLLECTION) {
      return 'All Patterns';
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
    return ucfirst($value);
  }

  public function pluralize($word) {
    return $word.'s';
  }
}