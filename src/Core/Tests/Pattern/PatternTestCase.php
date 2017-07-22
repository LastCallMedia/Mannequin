<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Pattern;

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\PatternVariant;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Variable\Definition;
use PHPUnit\Framework\TestCase;

abstract class PatternTestCase extends TestCase
{
    const PATTERN_ID = 'foo';

    const PATTERN_ALIASES = ['bar'];

    const TEMPLATE_FILE = '/foo/bar/baz';

    public function testGetId()
    {
        $this->assertEquals(static::PATTERN_ID, $this->getPattern()->getId());
    }

    abstract public function getPattern(): PatternInterface;

    public function testGetAliases()
    {
        $this->assertEquals(
            static::PATTERN_ALIASES,
            $this->getPattern()->getAliases()
        );
    }

    public function testGetSetName()
    {
        $pattern = $this->getPattern();
        $this->assertSame($pattern, $pattern->setName('Foobarbaz'));
        $this->assertEquals('Foobarbaz', $pattern->getName());
    }

    public function testPatternTagging()
    {
        $pattern = $this->getPattern();
        $this->assertInternalType('array', $pattern->getTags());
        $this->assertEquals($pattern, $pattern->addTag('foo', 'bar'));
        $this->assertArraySubset(['foo' => 'bar'], $pattern->getTags());
        $this->assertTrue($pattern->hasTag('foo', 'bar'));
        $this->assertFalse($pattern->hasTag('foo', 'baz'));
        $pattern->addTag('foo', 'baz');
        $this->assertTrue($pattern->hasTag('foo', 'baz'));
    }

    public function testVariableDefinition()
    {
        $definition = new Definition(['foo' => 'bar']);
        $pattern = $this->getPattern();
        $this->assertEquals(
            new Definition(),
            $pattern->getVariableDefinition()
        );
        $this->assertEquals(
            $pattern,
            $pattern->setVariableDefinition($definition)
        );
        $this->assertEquals($definition, $pattern->getVariableDefinition());
    }

    public function testVariants()
    {
        $pattern = $this->getPattern();
        $pattern->createVariant('default', 'Default', [], []);
        $this->assertEquals([
            'default' => new PatternVariant('default', 'Default'),
        ], $pattern->getVariants());

        $pattern->createVariant('default', 'Overridden', [], []);
        $this->assertEquals([
            'default' => new PatternVariant('default', 'Overridden'),
        ], $pattern->getVariants());
    }

    public function testGetFile()
    {
        $pattern = $this->getPattern();
        if ($pattern instanceof TemplateFilePatternInterface) {
            $this->assertInstanceOf(\SplFileInfo::class, $pattern->getFile());
            $this->assertEquals(
                self::TEMPLATE_FILE,
                $pattern->getFile()->getPathname()
            );
        }
    }
}
