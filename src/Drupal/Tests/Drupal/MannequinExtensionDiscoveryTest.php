<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests\Drupal;

use Drupal\Core\Extension\Extension;
use LastCall\Mannequin\Drupal\Drupal\MannequinExtensionDiscovery;
use LastCall\Mannequin\Drupal\Tests\UsesTestDrupalRoot;
use PHPUnit\Framework\TestCase;

class MannequinExtensionDiscoveryTest extends TestCase
{
    use UsesTestDrupalRoot;

    public static function setUpBeforeClass()
    {
        self::requireDrupalClasses();
        require_once sprintf('%s/core/includes/bootstrap.inc', self::getDrupalRoot());
    }

    public function testScan()
    {
        $discovery = new MannequinExtensionDiscovery(__DIR__);
        $this->assertEquals([], $discovery->scan('module', false));
    }

    public function testScanDiscoversModules()
    {
        $discovery = new MannequinExtensionDiscovery($this->getDrupalRoot());
        $modules = $discovery->scan('module');
        $this->assertInternalType('array', $modules);
        $this->assertTrue(count($modules) > 0);
        foreach ($modules as $module) {
            $this->assertInstanceOf(Extension::class, $module);
        }
    }
}
