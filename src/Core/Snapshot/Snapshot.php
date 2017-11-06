<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Snapshot;

class Snapshot implements \IteratorAggregate
{
    private $files;

    public function __construct(\Traversable $files)
    {
        $this->files = $files;
    }

    public function getIterator()
    {
        return $this->files;
    }
}
