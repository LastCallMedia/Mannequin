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
use Symfony\Component\EventDispatcher\Event;

class ComponentDiscoveryEvent extends Event
{
    private $component;
    private $collection;

    public function __construct(
        ComponentInterface $component,
        ComponentCollection $collection
    ) {
        $this->component = $component;
        $this->collection = $collection;
    }

    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    public function getCollection(): ComponentCollection
    {
        return $this->collection;
    }
}
