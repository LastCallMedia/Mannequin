<?php

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\Labeller;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;

class LabellerTest extends TestCase
{
    public function getCollectionTests()
    {
        return [
            [PatternCollection::ROOT_COLLECTION, 'All Patterns'],
            ['tag:foo:bar', 'Bars'],
        ];
    }

    /**
     * @dataProvider getCollectionTests
     */
    public function testGetCollectionLabel($id, $expected)
    {
        $collection = new PatternCollection([], $id);
        $this->assertEquals(
            $expected,
            (new Labeller())->getCollectionLabel($collection)
        );
    }

    public function testGetPatternLabel()
    {
        $pattern = $this->prophesize(PatternInterface::class);
        $pattern->getName()->willReturn('Foo');
        $pattern = $pattern->reveal();
        $this->assertEquals('Foo', (new Labeller())->getPatternLabel($pattern));
    }
}
