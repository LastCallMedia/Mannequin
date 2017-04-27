<?php

namespace LastCall\Patterns\Core\Tests\Pattern;

use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;

class PatternCollectionTest extends TestCase {

  public function testGetPatterns() {
    $mock = $this->prophesize(PatternInterface::class);
    $instance = $mock->reveal();
    $collection = new PatternCollection('test', 'Test', [$instance]);
    $this->assertEquals([$instance], $collection->getPatterns());
  }

  public function getPatternTests() {
    $mock = $this->prophesize(PatternInterface::class);
    $mock->getId()->willReturn('foo');
    $instance = $mock->reveal();

    return [
      [[$instance], 'foo', $instance],
      [[$instance], 'bar', NULL],
      [[], 'foo', NULL],
    ];
  }

  /**
   * @dataProvider getPatternTests
   */
  public function testGetPattern($patterns, $id, $expected) {
    $collection = new PatternCollection('test', 'Test', $patterns);
    $this->assertEquals($expected, $collection->getPattern($id));
  }

  public function testGetName() {
    $collection = new PatternCollection('test', 'Test');
    $this->assertEquals('Test', $collection->getName());
  }

  public function testGetId() {
    $collection = new PatternCollection('test', 'Test');
    $this->assertEquals('test', $collection->getId());
  }
}
