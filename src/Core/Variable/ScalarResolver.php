<?php


namespace LastCall\Mannequin\Core\Variable;


use LastCall\Mannequin\Core\Exception\InvalidVariableException;

class ScalarResolver implements ResolverInterface
{

    private static $supportedTypes = [
        'integer',
        'boolean',
        'string',
    ];

    public function resolves(string $type): bool
    {
        return in_array($type, $this::$supportedTypes);
    }

    public function resolve(string $type, $value)
    {
        switch ($type) {
            case 'integer':
                return (int)$value;
            case 'boolean':
                return (bool)$value;
            case 'string':
                return (string)$value;
        }
        throw new InvalidVariableException(
            sprintf('Invalid type %s passed to %s', $type, __CLASS__)
        );
    }
}