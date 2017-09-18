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
            $this->getRenderer()->supports($this->getSupportedComponent())
        );
        $this->assertFalse(
            $this->getRenderer()->supports($this->getUnsupportedComponent())
        );
    }

    abstract public function getRenderer(): EngineInterface;

    abstract public function getSupportedComponent(): ComponentInterface;

    protected function getUnsupportedComponent(): ComponentInterface
    {
        return $this->createComponent('unsupported')->reveal();
    }

    protected function createComponent($id)
    {
        $component = $this->prophesize(ComponentInterface::class);
        $component->getId()->willReturn($id);

        return $component;
    }

    public function testRender()
    {
        $component = $this->getSupportedComponent();
        $rendered = new Rendered();
        $this->getRenderer()->render(
            $component,
            [],
            $rendered
        );
        $this->assertInstanceOf(Rendered::class, $rendered);

        return $rendered;
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedComponentException
     */
    public function testRenderUnsupported()
    {
        $component = $this->getUnsupportedComponent();
        $this->getRenderer()->render(
            $component,
            [],
            new Rendered()
        );
    }

    public function testRenderSource()
    {
        $component = $this->getSupportedComponent();
        $source = $this->getRenderer()->renderSource(
            $component
        );
        $this->assertInternalType('string', $source);

        return $source;
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedComponentException
     */
    public function testRenderSourceUnsupported()
    {
        $component = $this->getUnsupportedComponent();
        $this->getRenderer()->renderSource(
            $component
        );
    }
}
