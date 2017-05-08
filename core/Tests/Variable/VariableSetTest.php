<?php


namespace LastCall\Patterns\Core\Tests\Variable;


use LastCall\Patterns\Core\Variable\ScalarType;
use LastCall\Patterns\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;

class VariableSetTest extends TestCase {

  private function getTestSet() {
    return new VariableSet([
      'foo' => new ScalarType('string', 'foo'),
      'empty' => new ScalarType('string')
    ]);
  }

  public function testHas() {
    $set = $this->getTestSet();
    $this->assertTrue($set->has('foo'));
    $this->assertTrue($set->has('empty'));
  }

  public function testMergesChanged() {
    $set1 = $this->getTestSet();
    $set2 = new VariableSet([
      'foo' => new ScalarType('string', 'bar'),
      'empty' => new ScalarType('string', 'empty'),
      'new' => new ScalarType('string', 'new'),
    ]);
    $merged = $set1->merge($set2);
    $this->assertEquals(new VariableSet([
      'foo' => new ScalarType('string', 'bar'),
      'empty' => new ScalarType('string', 'empty'),
      'new' => new ScalarType('string', 'new'),
    ]), $merged);
  }

  /**
   * @expectedException \LastCall\Patterns\Core\Exception\InvalidVariableException
   * @expectedExceptionMessage Cannot merge sets - foo is of a different type
   */
  public function testDoesNotAllowTypeChange() {
    $set1 = $this->getTestSet();
    $set2 = new VariableSet([
      'foo' => new ScalarType('integer', 1),
    ]);
    $set1->merge($set2);
  }

  public function testManifest() {
    $data = $this->getTestSet()->manifest();
    $this->assertEquals([
      'foo' => 'foo',
    ], $data);
  }
}