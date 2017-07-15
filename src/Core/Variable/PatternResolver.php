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
use LastCall\Mannequin\Core\Rendered;

class PatternResolver implements ResolverInterface
{
    private $renderFn;

    public function __construct(callable $renderFn)
    {
        $this->renderFn = $renderFn;
    }

    public function resolves(string $type): bool
    {
        return $type === 'pattern';
    }

    public function resolve(string $type, $value)
    {
        if ($type === 'pattern') {
            $fn = $this->renderFn;
            if (is_array($value)) {
                $id = $value['id'];
                $set = new Set('Nested', $value['value']);
            } else {
                $id = $value;
                $set = null;
            }
            $rendered = $fn($id, $set);
            if ($rendered instanceof Rendered) {
                return $rendered;
            }
            throw new \RuntimeException(
                sprintf(
                    'Pattern resolver callback did not return a valid value for %s',
                    $value
                )
            );
        }
        throw new InvalidVariableException(
            sprintf('Invalid type %s passed to %s', $type, __CLASS__)
        );
    }
}
