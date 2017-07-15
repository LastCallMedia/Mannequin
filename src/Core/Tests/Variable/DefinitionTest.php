<?php

namespace LastCall\Mannequin\Core\Tests\Variable;

use LastCall\Mannequin\Core\Variable\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    public function testHas()
    {
        $definition = new Definition(['foo' => 'bar']);
        $this->assertTrue($definition->has('foo'));
    }

    public function testGet()
    {
        $definition = new Definition(['foo' => 'bar']);
        $this->assertEquals('bar', $definition->get('foo'));
    }

    public function testKeys()
    {
        $definition = new Definition(['foo' => 'bar']);
        $this->assertEquals(['foo'], $definition->keys());
    }
}
