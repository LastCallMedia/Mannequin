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
    protected static function requireDrupalClasses()
    {
        if (!class_exists('\Drupal')) {
            self::markTestSkipped('Drupal classes do not exist.');
        }
    }

    protected static function getDrupalRoot()
    {
        $root = getenv('DRUPAL_ROOT');
        if (!$root) {
            self::markTestSkipped('No Drupal root given');
        }
        if (!is_dir($root)) {
            throw new \RuntimeException(sprintf('Drupal root %s does not exist', $root));
        }
        if ($root && !file_exists(sprintf('%s/core/includes/bootstrap.inc', $root))) {
            throw new \Exception(sprintf('Unable to detect Drupal root in %s', $root));
        }

        return $root;
    }

    abstract public static function markTestSkipped($message = '');
}
