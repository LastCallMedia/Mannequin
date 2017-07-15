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
