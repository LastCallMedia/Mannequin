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

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Handles resolving VariableSets and Variables to their native values.
 *
 * This class is only used right before patterns are rendered.
 */
final class VariableResolver
{
    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    public function resolve($variable, array $context = [])
    {
        if ($variable instanceof VariableSet) {
            return $this->resolveSet($variable, $context);
        } elseif ($variable instanceof Variable) {
            return $this->resolveVariable($variable, $context);
        }
    }

    private function resolveSet(VariableSet $set, array $context)
    {
        $resolved = [];
        foreach ($set as $key => $value) {
            $resolved[$key] = $this->resolve($value, $context);
        }

        return $resolved;
    }

    private function resolveVariable(Variable $variable, array $context)
    {
        if ($variable->getType() === 'expression') {
            return $this->expressionLanguage->evaluate($variable->getValue(), $context);
        } else {
            return $variable->getValue();
        }
    }
}
