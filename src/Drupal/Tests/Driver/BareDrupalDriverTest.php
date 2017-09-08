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

/**
 * This test is very slow because of the extension scanning.
 *
 * We share the driver between methods to avoid re-scanning wherever possible.
 */
class BareDrupalDriverTest extends TestCase
{
    use UsesTestDrupalRoot;

    public static function setUpBeforeClass()
    {
        self::requireDrupalClasses();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage a
     */
    public function testCreationWithInvalidDrupalRootFails()
    {
        new DrupalTwigDriver(__DIR__);
    }

    public function testGetTwig()
    {
        $driver = new DrupalTwigDriver($this->getDrupalRoot());
        $twig = $driver->getTwig();
        $this->assertInstanceOf(\Twig_Environment::class, $twig);

        return $driver;
    }

    /**
     * @depends testGetTwig
     */
    public function testGetTwigRoot(DrupalTwigDriver $driver)
    {
        $this->assertEquals($this->getDrupalRoot(), $driver->getTwigRoot());
    }

    /**
     * @depends testGetTwig
     */
    public function testGetNamespaces(DrupalTwigDriver $driver)
    {
        $namespaces = $driver->getNamespaces();
        $this->assertEquals(['.'], $namespaces['__main__']);
        $this->assertEquals(['core/modules/system/templates'], $namespaces['system']);
    }
}
