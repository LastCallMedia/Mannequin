<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Event;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\PatternVariant;
use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\EventDispatcher\Event;

class RenderEvent extends Event
{
    private $collection;
    private $pattern;
    private $variant;
    private $rendered;
    private $variables = [];

    public function __construct(PatternCollection $collection, PatternInterface $pattern, PatternVariant $variant, Rendered $rendered)
    {
        $this->collection = $collection;
        $this->pattern = $pattern;
        $this->variant = $variant;
        $this->rendered = $rendered;
    }

    public function getCollection(): PatternCollection
    {
        return $this->collection;
    }

    public function getPattern(): PatternInterface
    {
        return $this->pattern;
    }

    public function getVariant(): PatternVariant
    {
        return $this->variant;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getRendered(): Rendered
    {
        return $this->rendered;
    }

    public function setVariables(array $variables = [])
    {
        $this->variables = $variables;
    }
}
