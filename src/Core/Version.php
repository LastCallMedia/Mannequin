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

use SebastianBergmann\Version as VersionId;

/**
 * Determines the version of this package.
 *
 * Version is based on a stated tag (updated by the release packaging), and
 * git info, if available.
 */
class Version
{
    const TAG = '1.0.5';

    private static $version;

    public static function id(): string
    {
        if (null === self::$version) {
            $version = new VersionId(static::TAG, __DIR__);
            self::$version = $version->getVersion();
        }

        return self::$version;
    }
}
