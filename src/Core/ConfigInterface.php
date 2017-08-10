<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Psr\Cache\CacheItemPoolInterface;

interface ConfigInterface
{
    public function getCollection(): PatternCollection;

    public function addExtension(ExtensionInterface $extension
    ): ConfigInterface;

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array;

    public function getCache(): CacheItemPoolInterface;

    public function getUi(): UiInterface;

    public function getGlobalStyles(): array;

    public function getGlobalJs(): array;

    public function getGlobalAssets(): array;

    public function getAssetLibraries(): array;
}
