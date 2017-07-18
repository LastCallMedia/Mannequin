<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Variable;

use LastCall\Mannequin\Core\Exception\InvalidVariableException;

class VariableResolver
{
    /**
     * @var \LastCall\Mannequin\Core\Variable\ResolverInterface[]
     */
    private $resolvers = [];

    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    public function resolveSet(Definition $definition, array $values)
    {
        $resolved = [];

        foreach ($definition->keys() as $key) {
            $type = $definition->get($key);
            $resolver = $this->findResolver($type);
            if (array_key_exists($key, $values)) {
                $resolved[$key] = $resolver->resolve($type, $values[$key]);
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
        return (bool) $this->findResolver($type);
    }
}
