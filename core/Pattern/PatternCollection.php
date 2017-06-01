<?php


namespace LastCall\Mannequin\Core\Pattern;


class PatternCollection implements \Iterator, \Countable {

  const ROOT_COLLECTION = '__root__';

  private $id = self::ROOT_COLLECTION;

  /**
   * @var \LastCall\Mannequin\Core\Pattern\PatternInterface[]
   */
  private $patterns = [];

  private $aliases = [];

  private $parent;

  /**
   * PatternCollection constructor.
   *
   * @param array $patterns
   * @param string $id
   * @param string $name
   */
  public function __construct(array $patterns = [], string $id = self::ROOT_COLLECTION) {
    $this->id = $id;

    foreach($patterns as $pattern) {
      if(!$pattern instanceof PatternInterface) {
        throw new \RuntimeException('Pattern must be an instance of PatternInterface.');
      }
      $patternId = $pattern->getId();
      if(isset($this->patterns[$patternId])) {
        throw new \RuntimeException(sprintf('Duplicate pattern detected: %s', $patternId));
      }
      $this->patterns[$patternId] = $pattern;

      foreach($pattern->getAliases() as $alias) {
        if(isset($this->patterns[$alias])) {
          throw new \RuntimeException(sprintf('Alias %s would cause a duplicate pattern.', $alias));
        }
        if(isset($this->aliases[$alias])) {
          throw new \RuntimeException(sprintf('Alias %s would cause a duplicate pattern.', $alias));
        }
        $this->aliases[$alias] = $patternId;
      }
    }
  }

  public function getId(): string {
    return $this->id;
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

  public function has(string $id) {
    if(isset($this->patterns[$id])) {
      return TRUE;
    }
    if(isset($this->aliases[$id])) {
      return TRUE;
    }
    return FALSE;
  }

  public function get(string $id) {
    if(isset($this->patterns[$id])) {
      return $this->patterns[$id];
    }
    if(isset($this->aliases[$id])) {
      return $this->get($this->aliases[$id]);
    }
    throw new \RuntimeException(sprintf('Unknown pattern %s', $id));
  }

  private function setParent(PatternCollection $parent) {
    $this->parent = $parent;
  }

  /**
   * @return \LastCall\Mannequin\Core\Pattern\PatternCollection|null
   */
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
    $subCollection = new static($patterns, sprintf('tag:%s:%s', $type, $value), $name);
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
    $merged = new static($mergedPatterns, $this->id);
    if($this->parent) {
      $merged->setParent($this->parent);
    }
    return $merged;
  }
}