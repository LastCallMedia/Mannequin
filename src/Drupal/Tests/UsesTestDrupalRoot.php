<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests;

trait UsesTestDrupalRoot
{
    protected static function getDrupalRoot()
    {
        $root = getenv('DRUPAL_ROOT');
        if ($root && !file_exists(sprintf('%s/autoload.php'))) {
            throw new \Exception(sprintf('Unable to detect Drupal root in %s', $root));
        }

        return $root;
    }
}
