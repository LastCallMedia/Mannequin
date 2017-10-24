<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Config;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Ui\UiInterface;

interface ConfigInterface
{
    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array;

    public function getUi(): UiInterface;

    public function getGlobalCss(): array;

    public function getGlobalJs(): array;

    public function getAssets(): \Traversable;

    public function getCid(): string;

    public function getDocroot(): string;
}
