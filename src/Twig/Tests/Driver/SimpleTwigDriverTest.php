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

use LastCall\Mannequin\Twig\Driver\SimpleTwigDriver;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;

class SimpleTwigDriverTest extends DriverTestCase
{
    protected function getDriver(): TwigDriverInterface
    {
        return new SimpleTwigDriver(__DIR__, [], [
            'foo' => [__DIR__.'/../Resources'],
        ]);
    }

    public function testTwigHasLoader()
    {
        $loader = $this->getDriver()->getTwig()->getLoader();
        $expected = new \Twig_Loader_Filesystem([''], __DIR__);
        $expected->addPath(__DIR__.'/../Resources', 'foo');
        $this->assertEquals($expected, $loader);
    }
}
