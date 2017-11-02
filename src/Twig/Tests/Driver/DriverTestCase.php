<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Driver;

use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use PHPUnit\Framework\TestCase;

abstract class DriverTestCase extends TestCase
{
    abstract protected function getDriver(): TwigDriverInterface;

    public function testHasTwig()
    {
        $twig = $this->getDriver()->getTwig();
        $this->assertInstanceOf(\Twig_Environment::class, $twig);

        return $twig;
    }

    public function testHasTemplateNameMapper()
    {
        $mapper = $this->getDriver()->getTemplateNameMapper();
        $this->assertInternalType('callable', $mapper);

        return $mapper;
    }

    public function testHasAutoReload()
    {
        $twig = $this->getDriver()->getTwig();
        $this->assertTrue($twig->isAutoReload());
    }

    public function testTwigHasCache()
    {
        $cache = new \Twig_Cache_Null();
        $driver = $this->getDriver();
        $driver->setCache($cache);
        $this->assertSame($cache, $driver->getTwig()->getCache());
    }
}
