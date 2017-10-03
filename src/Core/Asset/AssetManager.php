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

use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use LastCall\Mannequin\Core\Iterator\RelativePathMapper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contains and writes the asset collection on demand.
 */
class AssetManager
{
    private $assets;
    private $srcRoot;
    private $filesystem;

    public function __construct(\Traversable $assets, string $assetRoot)
    {
        $this->assets = new MappingCallbackIterator($assets, new RelativePathMapper($assetRoot));
        $this->srcRoot = $assetRoot;
        $this->filesystem = new Filesystem();
    }

    public function write(string $targetDir)
    {
        $this->filesystem->mirror($this->srcRoot, $targetDir, $this->assets, [
            'overwrite' => false,
        ]);
    }

    public function get($path): \SplFileInfo
    {
        $path = ltrim($path, '\\/');
        foreach ($this->assets as $asset) {
            if ($asset->getRelativePathname() === $path) {
                return $asset;
            }
        }
        throw new NotFoundHttpException(sprintf('Unknown asset: %s', $path));
    }
}
