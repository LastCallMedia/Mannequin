<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Discovery;

use LastCall\Mannequin\Core\Component\ComponentCollection;

class ExplicitDiscovery implements DiscoveryInterface
{
    private $collection;

    public function __construct(ComponentCollection $collection)
    {
        $this->collection = $collection;
    }

    public function discover(): ComponentCollection
    {
        return $this->collection;
    }
}
