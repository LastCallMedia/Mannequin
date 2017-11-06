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

use Symfony\Component\Finder\SplFileInfo;

class SnapshotFile
{
    private $path;
    private $contents;

    public static function fromFileInfo(SplFileInfo $file)
    {
        return new static($file->getRelativePathname(), $file->getContents());
    }

    public function __construct(string $path, string $contents)
    {
        $this->path = $path;
        $this->contents = $contents;
    }

    public function getRelativePathname(): string
    {
        return $this->path;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
