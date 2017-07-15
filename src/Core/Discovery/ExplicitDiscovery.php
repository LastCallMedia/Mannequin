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

use LastCall\Mannequin\Core\Pattern\PatternCollection;

class ExplicitDiscovery implements DiscoveryInterface
{
    private $patternCollection;

    public function __construct(PatternCollection $collection)
    {
        $this->patternCollection = $collection;
    }

    public function discover(): PatternCollection
    {
        return $this->patternCollection;
    }
}
