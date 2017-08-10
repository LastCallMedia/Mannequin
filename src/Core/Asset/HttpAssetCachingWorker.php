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

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\WorkerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Replaces HTTP assets with locally cached copies of the same asset.
 *
 * NB: This will prevent changes from HTTP assets from being picked up.
 * If you have a use case that needs to fetch the assets live, do not
 * use this class.
 */
class HttpAssetCachingWorker implements WorkerInterface
{
    public function __construct()
    {
        $this->cacheDir = '/tmp/ass';
    }

    public function process(AssetInterface $asset, AssetFactory $factory)
    {
        if ($asset instanceof HttpAsset) {
            $filename = $this->getFilename($asset);
            if (!file_exists($filename)) {
                $this->download($asset, $filename);
            }

            return new FileAsset($filename, $asset->getFilters(), null, null, $asset->getVars());
        }
    }

    private function getFilename(HttpAsset $asset)
    {
        $url = $this->getUrl($asset);
        $filename = sprintf('%s/%s', $this->cacheDir, md5($url));
        if ('' !== $ext = pathinfo($url, PATHINFO_EXTENSION)) {
            $filename .= sprintf('.%s', $ext);
        }

        return $filename;
    }

    private function download(HttpAsset $asset, $filename)
    {
        (new Filesystem())->mkdir(dirname($filename));
        $asset->load();
        file_put_contents($filename, $asset->getContent());
    }

    private function getUrl(HttpAsset $asset)
    {
        return sprintf('%s/%s', $asset->getSourceRoot(), $asset->getSourcePath());
    }
}
