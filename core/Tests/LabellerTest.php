<?php

namespace LastCall\Patterns\Core\Tests;

use LastCall\Patterns\Core\Labeller;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;

class LabellerTest extends TestCase {

  public function getCollectionTests() {
    return [
      [PatternCollection::ROOT_COLLECTION, 'All Patterns'],
      ['tag:foo:bar', 'Bars'],
    ];
  }

  /**
   * @dataProvider getCollectionTests
   */
  public function testGetCollectionLabel($id, $expected) {
    $collection = new PatternCollection([], $id);
    $this->assertEquals($expected, (new Labeller())->getCollectionLabel($collection));
  }

  public function testGetPatternLabel() {
    $pattern = $this->prophesize(PatternInterface::class);
    $pattern->getName()->willReturn('Foo');
    $pattern = $pattern->reveal();
    $this->assertEquals('Foo', (new Labeller())->getPatternLabel($pattern));
  }
}
