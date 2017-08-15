<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Asset;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Contains and writes the asset collection on demand.
 */
class AssetManager
{
    private $assets;
    private $srcRoot;
    private $assetSubdir;
    private $filesystem;

    public function __construct(\Traversable $assets, string $assetRoot, string $assetSubdir = 'assets')
    {
        $this->assets = $assets;
        $this->srcRoot = $assetRoot;
        $this->assetSubdir = ltrim($assetSubdir, '/\\');
        $this->filesystem = new Filesystem();
    }

    public function write(string $targetDir)
    {
        $target = sprintf('%s%s%s', $targetDir, DIRECTORY_SEPARATOR, $this->assetSubdir);
        $this->filesystem->mirror($this->srcRoot, $target, $this->assets, [
            'overwrite' => false,
        ]);
    }
}
