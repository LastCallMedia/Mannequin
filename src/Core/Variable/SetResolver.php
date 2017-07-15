<?php


namespace LastCall\Mannequin\Core\Variable;


use LastCall\Mannequin\Core\Exception\InvalidVariableException;

class SetResolver
{

    /**
     * @var \LastCall\Mannequin\Core\Variable\ResolverInterface[]
     */
    private $resolvers = [];

    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    public function resolveSet(Definition $definition, Set $set)
    {
        $resolved = [];

        foreach ($definition->keys() as $key) {
            $type = $definition->get($key);
            $resolver = $this->findResolver($type);
            if ($set->has($key)) {
                $resolved[$key] = $resolver->resolve($type, $set->get($key));
            }
        }

        return $resolved;
    }

    private function findResolver(string $type, bool $throw = true)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->resolves($type)) {
                return $resolver;
            }
        }
        if ($throw) {
            throw new InvalidVariableException(
                sprintf('No resolver knows how to resolve a %s variable', $type)
            );
        }
    }

    public function resolves($type)
    {
        return (bool)$this->findResolver($type);
    }
}