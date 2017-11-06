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

use LastCall\Mannequin\Core\Asset\AssetManagerInterface;
use LastCall\Mannequin\Core\Component\ComponentCollection;

/**
 * Defines an object that snapshots a collection with all of its assets.
 */
interface CameraInterface
{
    /**
     * Snapshot a collection.
     *
     * When an error happens during snapshotting of a single component, the
     * errorHandler will be invoked.  The errorHandler can either do something
     * with the error, or rethrow it to stop rendering.  The signature of the
     * errorHandler function is:
     *
     *    function(\Exception $e, ComponentInterface $component) {}
     *
     * @param \LastCall\Mannequin\Core\Component\ComponentCollection $collection
     * @param \LastCall\Mannequin\Core\Asset\AssetManagerInterface   $manager
     * @param callable|null                                          $errorHandler a PHP callable
     *
     * @return \LastCall\Mannequin\Core\Snapshot\Snapshot
     */
    public function snapshot(ComponentCollection $collection, AssetManagerInterface $manager, callable $errorHandler = null): Snapshot;
}
