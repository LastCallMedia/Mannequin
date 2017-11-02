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

use LastCall\Mannequin\Twig\Driver\PreloadedTwigDriver;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\TemplateNameMapper;

class PreloadedTwigDriverTest extends DriverTestCase
{
    protected function getDriver(): TwigDriverInterface
    {
        $loader = new \Twig_Loader_Filesystem([__DIR__], __DIR__);
        $twig = new \Twig_Environment($loader);

        return new PreloadedTwigDriver($twig, __DIR__, [
            'foo' => [__DIR__.'/bar'],
        ]);
    }

    public function testHasTemplateNameMapper()
    {
        $mapper = parent::testHasTemplateNameMapper();
        $expected = new TemplateNameMapper(__DIR__);
        $expected->addNamespace('foo', [__DIR__.'/bar']);
        $this->assertEquals($expected, $mapper);
    }
}
