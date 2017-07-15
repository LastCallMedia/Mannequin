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

class Definition
{
    private $definitions = [];

    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $name => $type) {
            $this->definitions[$name] = $type;
        }
    }

    public function has($name)
    {
        return isset($this->definitions[$name]);
    }

    public function get($name)
    {
        return $this->definitions[$name];
    }

    public function keys()
    {
        return array_keys($this->definitions);
    }
}
