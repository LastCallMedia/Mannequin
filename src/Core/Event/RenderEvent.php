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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\EventDispatcher\Event;

class RenderEvent extends Event
{
    private $collection;
    private $component;
    private $sample;
    private $rendered;
    private $variables = [];
    private $isRoot;

    public function __construct(ComponentCollection $collection, ComponentInterface $component, Sample $sample, Rendered $rendered, $isRoot = true)
    {
        $this->collection = $collection;
        $this->component = $component;
        $this->sample = $sample;
        $this->rendered = $rendered;
        $this->isRoot = $isRoot;
    }

    public function getCollection(): ComponentCollection
    {
        return $this->collection;
    }

    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    public function getSample(): Sample
    {
        return $this->sample;
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

    public function isRoot(): bool
    {
        return $this->isRoot;
    }
}
