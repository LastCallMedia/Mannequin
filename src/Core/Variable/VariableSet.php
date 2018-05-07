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
 * A set (array) of named, unresolved variables.  Can also contain nested sets.
 */
final class VariableSet implements \ArrayAccess, \IteratorAggregate
{
    private $values = [];

    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset] ?: null;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * Merge another variable set with this one.
     *
     * The merging set will be combined with the current set, with the
     * merging set's values being chosen over the current set's values
     * wherever the same key is detected.
     *
     * @param \LastCall\Mannequin\Core\Variable\VariableSet $merging
     *
     * @return \LastCall\Mannequin\Core\Variable\VariableSet
     */
    public function merge(VariableSet $merging)
    {
        return new VariableSet($merging->values + $this->values);
    }
}
