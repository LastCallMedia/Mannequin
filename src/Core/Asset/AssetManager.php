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
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contains and writes the asset collection on demand.
 */
class AssetManager implements AssetManagerInterface
{
    private $assets;

    public function __construct(\Traversable $assets, string $assetRoot)
    {
        $this->assets = new MappingCallbackIterator($assets, new RelativePathMapper($assetRoot));
    }

    /**
     * Get a single relative asset.
     *
     * @param string $path the relative path to the asset
     *
     * @return SplFileInfo
     */
    public function get(string $path): SplFileInfo
    {
        $path = ltrim($path, '\\/');
        foreach ($this->assets as $asset) {
            if ($asset->getRelativePathname() === $path) {
                return $asset;
            }
        }
        throw new NotFoundHttpException(sprintf('Unknown asset: %s', $path));
    }

    /**
     * Get all the assets this manager knows about.
     *
     * The assets will be converted to relative SplFileInfo assets on their
     * way out.
     *
     * @return \Symfony\Component\Finder\SplFileInfo[] iterator of assets
     */
    public function getIterator()
    {
        return $this->assets;
    }
}
