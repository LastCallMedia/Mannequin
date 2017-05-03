<?php


namespace LastCall\Patterns\Core\Pattern;


class PatternCollection implements \Iterator, \Countable {

  use HasNameAndId;

  /**
   * @var \LastCall\Patterns\Core\Pattern\PatternInterface[]
   */
  private $patterns = [];

  private $parent;

  /**
   * PatternCollection constructor.
   *
   * @param array $patterns
   * @param string $id
   * @param string $name
   */
  public function __construct(array $patterns = [], $id = 'default', $name = 'Default') {
    $this->setId($id);
    $this->setName($name);
    foreach($patterns as $pattern) {
      if(!$pattern instanceof PatternInterface) {
        throw new \RuntimeException('Pattern must be an instance of PatternInterface.');
      }
      $id = $pattern->getId();
      if(isset($this->patterns[$id])) {
        throw new \RuntimeException(sprintf('Duplicate pattern detected: %s', $id));
      }
      $this->patterns[$id] = $pattern;
    }
  }

  public function rewind() {
    return reset($this->patterns);
  }

  public function valid() {
    return key($this->patterns) !== NULL;
  }

  public function next() {
    return next($this->patterns);
  }

  public function current() {
    return current($this->patterns);
  }

  public function key() {
    return key($this->patterns);
  }

  public function count() {
    return count($this->patterns);
  }

  public function get(string $id) {
    if(isset($this->patterns[$id])) {
      return $this->patterns[$id];
    }
    throw new \RuntimeException(sprintf('Unknown pattern %s', $id));
  }

  private function setParent(PatternCollection $parent) {
    $this->parent = $parent;
  }

  public function getParent() {
    return $this->parent;
  }

  public function getPatterns() {
    return array_values($this->patterns);
  }

  public function withTag($type, $value, $name = NULL) {
    $patterns = array_filter($this->patterns, function(PatternInterface $pattern) use ($type, $value) {
      return $pattern->hasTag($type, $value);
    });

    $name = $name ?: $value;
    $subCollection = new static($patterns, sprintf('%s:%s', $type, $value), $name);
    $subCollection->setParent($this);
    return $subCollection;
  }

  /**
   * @todo: Is this method useful?
   */
  public function getTags() {
    return array_reduce($this->patterns, function($carry, PatternInterface $pattern) {
      foreach($pattern->getTags() as $name => $value) {
        if(!array_key_exists($name, $carry)) {
          $carry[$name] = [];
        }
        if(FALSE === array_search($value, $carry[$name])) {
          $carry[$name][] = $value;
        }
      }
      return $carry;
    }, []);
  }

  public function withPattern($id) {
    if(isset($this->patterns[$id])) {
      $subCollection = new static([$this->patterns[$id]], sprintf('pattern:%s', $id), 'Pattern');
      $subCollection->setParent($this);
      return $subCollection;
    }
  }

  public function merge(PatternCollection $merging) {
    $overlapping = array_intersect(array_keys($this->patterns), array_keys($merging->patterns));
    if(count($overlapping)) {
      throw new \RuntimeException(sprintf('Merging these collections would result in the following duplicate patterns: %s', implode(', ', $overlapping)));
    }
    $mergedPatterns = array_merge($this->patterns, $merging->patterns);
    $merged = new static($mergedPatterns, $this->id, $this->name);
    if($this->parent) {
      $merged->setParent($this->parent);
    }
    return $merged;
  }
}