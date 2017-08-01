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

use LastCall\Mannequin\Core\Variable\VariableSet;

class PatternVariant
{
    private $id;
    private $name;
    private $values = [];
    private $tags = [];
    private $variables;

    public function __construct($id, $name, VariableSet $set = null, array $tags = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->tags = $tags;
        $this->variables = $set ?: new VariableSet();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @deprecated
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getVariables(): VariableSet
    {
        return $this->variables;
    }
}
