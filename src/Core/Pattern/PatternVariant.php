<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Pattern;

class PatternVariant
{
    private $id;
    private $name;
    private $values = [];
    private $tags = [];

    public function __construct($id, $name, array $values = [], array $tags = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->values = $values;
        $this->tags = $tags;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
