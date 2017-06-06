<?php

namespace LastCall\Mannequin\Core\Iterator;

/**
 * Iterator that invokes a callback for each item in order to resolve the path
 * to a template file.
 *
 * This is useful if you want to iterate through a list of absolute paths and
 * convert them into a relative path or a name by which a particular discoverer
 * will know them.
 */
class MappingCallbackIterator extends \IteratorIterator {

  private $resolver;

  public function __construct(\Traversable $iterator, callable $resolver) {
    parent::__construct($iterator);
    $this->resolver = $resolver;
  }

  public function current() {
    $resolver = $this->resolver;
    return $resolver(parent::current());
  }
}