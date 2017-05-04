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
    if(preg_match('/type:(.*)/', $id, $matches)) {
      return ucfirst($matches[1]) .'s';
    }
    return $id;
  }

  public function getPatternLabel(PatternInterface $pattern) {
    return $pattern->getName();
  }
}