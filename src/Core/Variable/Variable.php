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
 * A single unresolved variable.  Typically either 'simple' or 'expression.
 */
final class Variable
{
    private $type;
    private $value;

    public function __construct(string $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Get the type of this variable.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the value of this variable.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
