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

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Drupal\DrupalExtension;

class DrupalExtensionTest extends ExtensionTestCase
{
    use UsesTestDrupalRoot;

    public static function setUpBeforeClass()
    {
        if (!$root = self::getDrupalRoot()) {
            self::markTestSkipped('Drupal root not given.');
        }
        if (!file_exists(self::getDrupalRoot().'/sites/default/settings.php')) {
            copy(self::getDrupalRoot().'/sites/default/default.settings.php', self::getDrupalRoot().'/sites/default/settings.php');
        }
    }

    public function setUp()
    {
        $this->markTestSkipped('Drupal tests are still in progress.');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to detect Drupal installation in
     */
    public function testCreationWithInvalidDrupalRootFails()
    {
        new DrupalExtension();
    }

    public function getExtension(): ExtensionInterface
    {
        return new DrupalExtension([
            'drupal_root' => $this->getDrupalRoot(),
        ]);
    }
}
