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

/**
 * Represents a Mannequin configuration, typically returned from a
 * .mannequin.php file.
 */
interface ConfigInterface
{
    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array;

    /**
     * Get the UI object this configuration uses.
     *
     * @return \LastCall\Mannequin\Core\Ui\UiInterface
     */
    public function getUi(): UiInterface;

    /**
     * Get the CSS paths/urls that are used for all components rendered by this
     * configuration.
     *
     * @return array
     */
    public function getGlobalCss(): array;

    /**
     * Get the javascript paths/urls that are used for all components rendered
     * by this configuration.
     *
     * @return array
     */
    public function getGlobalJs(): array;

    /**
     * Get a traversable list of the assets that should be copied to any
     * snapshots rendered under this configuration.
     *
     * @return \Traversable
     */
    public function getAssets(): \Traversable;

    /**
     * Get an identifier to use for distinguishing cache entries stored for
     * multiple Mannequin projects.
     *
     * @return string
     */
    public function getCachePrefix(): string;

    /**
     * Set the identifier to use for distinguishing cache entries stored for
     * multiple Mannequin projects.
     *
     * @param string $prefix
     *
     * @return static
     */
    public function setCachePrefix(string $prefix): ConfigInterface;

    /**
     * Get the path to use as the docroot for this configuration.
     *
     * Relative asset paths are resolved from this path.
     *
     * @return string
     */
    public function getDocroot(): string;

    /**
     * Set the path to use as the docroot for this configuration.
     *
     * @param string $docroot
     *
     * @return static
     */
    public function setDocroot(string $docroot): ConfigInterface;
}
