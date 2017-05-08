<?php


namespace LastCall\Patterns\Core\Tests\Variable;


use LastCall\Patterns\Core\Variable\ScalarType;
use LastCall\Patterns\Core\Variable\VariableFactory;
use PHPUnit\Framework\TestCase;

class VariableFactoryTest extends TestCase {

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage stdClass does not implement LastCall\Patterns\Core\Variable\VariableInterface
   */
  public function testRegisterInvalidType() {
    new VariableFactory(['stdClass']);
  }

  public function testRegisterValidType() {
    $factory = new VariableFactory([ScalarType::class]);
    $this->assertTrue($factory->hasType('string'));
    $this->assertTrue($factory->hasType('boolean'));
  }

  public function testCreate() {
    $factory = new VariableFactory([ScalarType::class]);
    $created = $factory->create('string', 'foo');
    $this->assertInstanceOf(ScalarType::class, $created);
    $this->assertEquals('foo', $created->getValue());
    $this->assertEquals('string', $created->getTypeName());
  }

  /**
   * @expectedException \LastCall\Patterns\Core\Exception\InvalidVariableException
   * @expectedExceptionMessage string is not a valid variable type
   */
  public function testCreateInvalidType() {
    $factory = new VariableFactory();
    $factory->create('string', 'foo');
  }
}