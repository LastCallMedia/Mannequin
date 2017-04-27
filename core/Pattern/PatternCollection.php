<?php


namespace LastCall\Patterns\Core\Pattern;


class PatternCollection {

  use HasNameAndId;

  /**
   * @var \LastCall\Patterns\Core\Pattern\PatternInterface[]
   */
  private $patterns = [];

  /**
   * PatternCollection constructor.
   *
   * @param \LastCall\Patterns\Pattern\PatternInterface[]
   */
  public function __construct($id = 'default', $name = 'Default', array $patterns = []) {
    $this->setId($id);
    $this->setName($name);
    $this->patterns = $patterns;
  }

  public function getPatterns() {
    return $this->patterns;
  }

  public function addPattern(PatternInterface $pattern) {
    $this->patterns[] = $pattern;
  }

  public function getPattern(string $id) {
    $matching = array_filter($this->patterns, function(PatternInterface $pattern) use ($id) {
      return $id === $pattern->getId();
    });
    return $matching ? reset($matching) : NULL;
  }
}