<?php


namespace LastCall\Mannequin\Core\Variable;


class PatternResolver implements ResolverInterface {

  public function __construct(callable $renderFn) {
    $this->renderFn = $renderFn;
  }


  public function validate(string $type, $value) {
    // TODO: Implement validate() method.
  }

  public function resolves(string $type): bool {
    return $type === 'pattern';
  }

  public function resolve(string $type, $value) {
    return '';
  }
}