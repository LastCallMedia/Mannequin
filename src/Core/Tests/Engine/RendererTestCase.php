<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Engine;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Rendered;
use PHPUnit\Framework\TestCase;

abstract class RendererTestCase extends TestCase
{
    public function testSupports()
    {
        $this->assertTrue(
            $this->getRenderer()->supports($this->getSupportedPattern())
        );
        $this->assertFalse(
            $this->getRenderer()->supports($this->getUnsupportedPattern())
        );
    }

    abstract public function getRenderer(): EngineInterface;

    abstract public function getSupportedPattern(): ComponentInterface;

    protected function getUnsupportedPattern(): ComponentInterface
    {
        return $this->createPattern('unsupported')->reveal();
    }

    protected function createPattern($id)
    {
        $pattern = $this->prophesize(ComponentInterface::class);
        $pattern->getId()->willReturn($id);

        return $pattern;
    }

    public function testRender()
    {
        $pattern = $this->getSupportedPattern();
        $rendered = new Rendered();
        $this->getRenderer()->render(
            $pattern,
            [],
            $rendered
        );
        $this->assertInstanceOf(Rendered::class, $rendered);

        return $rendered;
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     */
    public function testRenderUnsupported()
    {
        $pattern = $this->getUnsupportedPattern();
        $this->getRenderer()->render(
            $pattern,
            [],
            new Rendered()
        );
    }

    public function testRenderSource()
    {
        $pattern = $this->getSupportedPattern();
        $source = $this->getRenderer()->renderSource(
            $pattern
        );
        $this->assertInternalType('string', $source);

        return $source;
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     */
    public function testRenderSourceUnsupported()
    {
        $pattern = $this->getUnsupportedPattern();
        $this->getRenderer()->renderSource(
            $pattern
        );
    }
}
