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

/**
 * Defines a configuration object that can be reloaded inside a new PHP process.
 *
 * The two pieces we need to reload are:
 *   - The autoload.php file that was used in initially loading the object.
 *   - The file that causes the instantiation of the object.
 *
 * In most cases, we don't actually need our config to be readdressable, but
 * for the development server, we do.  In all other instances, we should be
 * type hinting for ConfigInterface, not ReaddressableConfigInterface.
 */
interface ReaddressableConfigInterface extends ConfigInterface
{
    /**
     * Return the file that.
     *
     * @return string
     */
    public function getSourceFile(): string;

    public function getAutoloadFile(): string;
}
