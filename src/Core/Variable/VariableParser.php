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

/**
 * Parses string representations of variables into VariableSet and Variable
 * objects.
 *
 * Only two types of variable are handled by this parser:
 *
 *   simple - A scalar, object, or array value that will not be resolved further.
 *   expression - An ExpressionLanguage string that will be resolved before
 *     rendering.
 */
final class VariableParser
{
    /**
     * Parse a string or array into a set or variable.
     */
    public function parse($value)
    {
        if (is_array($value)) {
            return $this->parseSet($value);
        } else {
            return $this->parseValue($value);
        }
    }

    private function parseSet(array $values)
    {
        $set = new VariableSet();
        foreach ($values as $key => $value) {
            $set[$key] = $this->parse($value);
        }

        return $set;
    }

    private function parseValue($value)
    {
        if (is_string($value) && 0 === strpos($value, '~')) {
            return new Variable('expression', substr($value, 1));
        }

        return new Variable('simple', $value);
    }
}
