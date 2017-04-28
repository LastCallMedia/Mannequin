<?php

namespace LastCall\Patterns\Core\Tests\Pattern;

use LastCall\Patterns\Core\Pattern\Pattern;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use PHPUnit\Framework\TestCase;

class PatternCollectionTest extends TestCase {

  public function testIsIterator() {
    $collection = new PatternCollection();
    $this->assertInstanceOf('Iterator', $collection);
  }

  public function testIsCountable() {
    $collection = new PatternCollection();
    $this->assertInstanceOf('Countable', $collection);
    $this->assertEquals(0, $collection->count());
  }

  public function testDefaultIdName() {
    $collection = new PatternCollection();
    $this->assertEquals('default', $collection->getId());
    $this->assertEquals('Default', $collection->getName());
  }

  public function testGetParent() {
    $collection = new PatternCollection();
    $this->assertEquals(NULL, $collection->getParent());
  }

  public function testIteration() {
    $pattern1 = new Pattern('foo', 'bar', 'baz');
    $pattern2 = new Pattern('bar', 'baz', 'boo');

    $collection = new PatternCollection([$pattern1, $pattern2]);
    $patterns = [];
    foreach($collection as $pattern) {
      $patterns[] = $pattern;
    }
    $this->assertEquals([$pattern1, $pattern2], $patterns);
    $collection->rewind();

    $patterns = [];
    foreach($collection as $pattern) {
      $patterns[] = $pattern;
    }
    $this->assertEquals([$pattern1, $pattern2], $patterns);
  }

  public function testGet() {
    $pattern = new Pattern('foo', 'bar', 'baz');
    $collection = new PatternCollection([$pattern]);
    $this->assertEquals($pattern, $collection->get('foo'));
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage Unknown pattern bar
   */
  public function testGetInvalid() {
    $pattern = new Pattern('foo', 'bar', 'baz');
    $collection = new PatternCollection([$pattern]);
    $this->assertEquals($pattern, $collection->get('bar'));
  }

  public function getInvalidPatterns() {
    return [
      [['foo']],
      [[new \stdClass()]]
    ];
  }

  /**
   * @dataProvider getInvalidPatterns
   * @expectedException \RuntimeException
   */
  public function testCreateWithInvalidPatterns(array $patterns) {
    new PatternCollection($patterns);
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage Duplicate pattern detected: foo
   */
  public function testCreateWithDuplicatePatterns() {
    $pattern1 = new Pattern('foo', 'bar', 'baz');
    $pattern2 = new Pattern('foo', 'baz', 'bar');
    new PatternCollection([$pattern1, $pattern2]);
  }

  public function testWithTag() {
    $pattern = new Pattern('foo', 'bar', 'baz');
    $pattern->addTag('type', 'element');
    $collection = new PatternCollection([$pattern]);
    $tagCollection = $collection->withTag('type', 'element');
    $this->assertEquals(1, $tagCollection->count());
    $this->assertEquals('tag:type:element', $tagCollection->getId());
    $this->assertEquals($collection, $tagCollection->getParent());
  }

  public function testWithTagEmpty() {
    $collection = new PatternCollection();
    $this->assertNull($collection->withTag('type', 'element'));
  }

  public function testMergeMergesPatterns() {
    $pattern1 = new Pattern('foo', 'bar', 'baz');
    $pattern2 = new Pattern('bar', 'baz', 'boo');
    $collection1 = new PatternCollection([$pattern1]);
    $collection2 = new PatternCollection([$pattern2]);
    $merged = $collection1->merge($collection2);
    $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
  }

  public function testMergeKeepsNameAndId() {
    $collection1 = new PatternCollection([], 'collection1','Collection 1');
    $collection2 = new PatternCollection([], 'collection2', 'Collection 2');
    $merged = $collection1->merge($collection2);
    $this->assertEquals('collection1', $merged->getId());
    $this->assertEquals('Collection 1', $merged->getName());
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage Merging these collections would result in the following duplicate patterns: foo
   */
  public function testMergeMergesSamePatterns() {
    $pattern1 = new Pattern('foo', 'bar', 'baz');
    $pattern2 = new Pattern('foo', 'baz', 'boo');
    $collection1 = new PatternCollection([$pattern1]);
    $collection2 = new PatternCollection([$pattern2]);
    $merged = $collection1->merge($collection2);
    $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
  }
}
