<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Component;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Component\TemplateFileInterface;
use PHPUnit\Framework\TestCase;

abstract class ComponentTestCase extends TestCase
{
    const COMPONENT_ID = 'foo';

    const COMPONENT_ALIASES = ['bar'];

    const TEMPLATE_FILE = '/foo/bar/baz';

    public function testGetId()
    {
        $this->assertEquals(static::COMPONENT_ID, $this->getComponent()->getId());
    }

    abstract public function getComponent(): ComponentInterface;

    public function testGetAliases()
    {
        $this->assertEquals(
            static::COMPONENT_ALIASES,
            $this->getComponent()->getAliases()
        );
    }

    public function testGetSetName()
    {
        $component = $this->getComponent();
        $this->assertSame($component, $component->setName('Foobarbaz'));
        $this->assertEquals('Foobarbaz', $component->getName());
    }

    public function testComponentMetadata()
    {
        $component = $this->getComponent();
        $this->assertEquals($component, $component->addMetadata('foo', 'bar'));
        $this->assertArraySubset(['foo' => 'bar'], $component->getMetadata());
        $this->assertTrue($component->hasMetadata('foo', 'bar'));
        $this->assertFalse($component->hasMetadata('foo', 'baz'));
        $component->addMetadata('foo', 'baz');
        $this->assertTrue($component->hasMetadata('foo', 'baz'));
    }

    public function testSamples()
    {
        $component = $this->getComponent();
        $component->createSample('default', 'Default');
        $this->assertEquals([
            'default' => new \LastCall\Mannequin\Core\Component\Sample('default', 'Default'),
        ], $component->getSamples());

        $component->createSample('default', 'Overridden');
        $this->assertEquals([
            'default' => new Sample('default', 'Overridden'),
        ], $component->getSamples());
    }

    public function testGetFile()
    {
        $component = $this->getComponent();
        if ($component instanceof TemplateFileInterface) {
            $this->assertInstanceOf(\SplFileInfo::class, $component->getFile());
            $this->assertEquals(
                static::TEMPLATE_FILE,
                $component->getFile()->getPathname()
            );
        }
    }
}
