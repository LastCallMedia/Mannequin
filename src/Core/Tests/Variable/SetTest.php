<?php

namespace LastCall\Mannequin\Core\Tests\Variable;

use LastCall\Mannequin\Core\Variable\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    public function testName()
    {
        $set = new Set('Test set');
        $this->assertEquals('Test set', $set->getName());
    }

    public function testDescription()
    {
        $set = new Set('Test', [], 'My description');
        $this->assertEquals('My description', $set->getDescription());
    }

    public function testHas()
    {
        $set = new Set('Test', ['foo' => 'bar']);
        $this->assertTrue($set->has('foo'));
    }

    public function testGet()
    {
        $set = new Set('Test', ['foo' => 'bar']);
        $this->assertEquals('bar', $set->get('foo'));
    }
}
