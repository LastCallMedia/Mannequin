<?php


namespace LastCall\Mannequin\Core\Tests\Variable;


use LastCall\Mannequin\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;

class VariableSetTest extends TestCase
{
    public function testMerge() {
        $s1 = new VariableSet([
            'foo' => 1,
            'baz' => 1
        ]);
        $s2 = new VariableSet([
            'bar' => 2,
            'baz' => 2,
        ]);
        $merged = $s1->merge($s2);
        $expected = new VariableSet([
            'foo' => 1,
            'bar' => 2,
            'baz' => 2,
        ]);
        $this->assertEquals($expected, $merged);
    }

}