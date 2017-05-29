<?php


namespace LastCall\Mannequin\Core\Variable;

use LastCall\Mannequin\Core\Exception\InvalidVariableException;
use LastCall\Mannequin\Core\Rendered;


class PatternResolver implements ResolverInterface {

  private $renderFn;

  public function __construct(callable $renderFn) {
    $this->renderFn = $renderFn;
  }

  public function resolves(string $type): bool {
    return $type === 'pattern';
  }

  public function resolve(string $type, $value) {
    if($type === 'pattern') {
      $fn = $this->renderFn;
      $rendered = $fn($value);
      if($rendered instanceof Rendered) {
        return $rendered;
      }
      throw new \RuntimeException(sprintf('Pattern resolver callback did not return a valid value for %s', $value));
    }
    throw new InvalidVariableException(sprintf('Invalid type %s passed to %s', $type, __CLASS__));

  }
}