<?php


namespace LastCall\Mannequin\Core\Tests\Variable;


use LastCall\Mannequin\Core\Variable\ScalarFactory;
use LastCall\Mannequin\Core\Variable\ScalarType;
use LastCall\Mannequin\Core\Variable\VariableFactory;
use PHPUnit\Framework\TestCase;

class VariableFactoryTest extends TestCase {

  /**
   * @expectedException TypeError
   * @expectedExceptionMessage must be callable
   */
  public function testRegisterInvalidType() {
    (new VariableFactory())->addType('foo', 'bar');
  }

  public function testRegisterValidType() {
    $factory = new VariableFactory();
    $factory->addFactory(new ScalarFactory());
    $this->assertTrue($factory->hasType('string'));
    $this->assertTrue($factory->hasType('boolean'));
  }

  public function testCreate() {
    $factory = new VariableFactory([], [new ScalarFactory()]);
    $created = $factory->create('string', 'foo');
    $this->assertInstanceOf(ScalarType::class, $created);
    $this->assertEquals('foo', $created->getValue());
    $this->assertEquals('string', $created->getTypeName());
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\InvalidVariableException
   * @expectedExceptionMessage string is not a valid variable type
   */
  public function testCreateInvalidType() {
    $factory = new VariableFactory();
    $factory->create('string', 'foo');
  }
}