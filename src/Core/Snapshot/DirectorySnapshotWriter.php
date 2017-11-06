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

use Symfony\Component\Filesystem\Filesystem;

/**
 * Writes snapshots to a given directory.
 */
class DirectorySnapshotWriter implements SnapshotWriterInterface
{
    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function write(Snapshot $snapshot)
    {
        $fs = new Filesystem();
        foreach ($snapshot as $file) {
            $dest = sprintf('%s/%s', $this->dir, $file->getRelativePathname());
            $fs->dumpFile($dest, $file->getContents());
        }
    }
}
