<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests\Driver;

use LastCall\Mannequin\Drupal\Driver\DrupalTwigDriver;
use LastCall\Mannequin\Drupal\Tests\UsesTestDrupalRoot;
use PHPUnit\Framework\TestCase;

class BareDrupalDriverTest extends TestCase
{
    use UsesTestDrupalRoot;

    private $root;

    public function setUp()
    {
        if (!$root = self::getDrupalRoot()) {
            $this->markTestSkipped('No Drupal root given.');
        }
        $this->root = $root;
    }

    public function testGetTwig()
    {
        $driver = new DrupalTwigDriver($this->root);
        $twig = $driver->getTwig();
        $this->assertInstanceOf(\Twig_Environment::class, $twig);
    }

    public function testGetLoader()
    {
        $driver = new DrupalTwigDriver($this->root);
        $loader = $driver->getTwigLoader();
        $this->assertInstanceOf(\Twig_LoaderInterface::class, $loader);
    }
}
