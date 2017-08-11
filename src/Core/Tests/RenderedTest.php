<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\Rendered;
use PHPUnit\Framework\TestCase;

class RenderedTest extends TestCase
{
    public function testMarkup()
    {
        $rendered = new Rendered();
        $rendered->setMarkup('foo');
        $this->assertEquals('foo', $rendered->getMarkup());
        $this->assertEquals('foo', (string) $rendered);
    }

    public function testStyles()
    {
        $rendered = new Rendered();
        $rendered->setCss(['foo']);
        $rendered->addCss(['bar']);
        $this->assertEquals(['foo', 'bar'], $rendered->getCss());
    }

    public function testScripts()
    {
        $rendered = new Rendered();
        $rendered->setJs(['foo']);
        $rendered->addJs(['bar']);
        $this->assertEquals(['foo', 'bar'], $rendered->getJs());
    }
}
