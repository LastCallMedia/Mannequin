<?php


namespace LastCall\Mannequin\Core\Variable;


class SetResolver {

  private $resolvers = [];

  public function __construct(array $resolvers = []) {
    $this->resolvers = $resolvers;
  }

  public function resolveSet(Definition $definition, Set $set) {
    $resolved = [];

    foreach($definition->keys() as $key) {
      if($set->has($key)) {
        foreach($this->resolvers as $resolver) {
          $type = $definition->get($key);
          if($resolver->resolves($type)) {
            $resolved[$key] = $resolver->resolve($type, $set->get($key));
            break;
          }
        }
      }
    }
    return $resolved;
  }

  public function resolves($type) {
    foreach($this->resolvers as $resolver) {
      if($resolver->resolves($type)) {
        return TRUE;
      }
    }
    return FALSE;
  }
}