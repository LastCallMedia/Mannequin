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

class Set
{
    private $name;

    private $description = '';

    private $values;

    public function __construct(
        string $name,
        array $values = [],
        $description = ''
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function has(string $name)
    {
        return isset($this->values[$name]);
    }

    public function get(string $name)
    {
        return $this->values[$name];
    }
}
