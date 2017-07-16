<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
