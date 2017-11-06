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

/**
 * Writes snapshot files to a destination.
 *
 * In the future, we want to support writing snapshots directly to places like
 * a zip file, Amazon S3, or other remote sources.
 */
interface SnapshotWriterInterface
{
    public function write(Snapshot $snapshot);
}
