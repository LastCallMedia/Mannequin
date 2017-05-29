<?php


namespace LastCall\Mannequin\Core\Tests\Variable;


use LastCall\Mannequin\Core\Variable\ScalarResolver;
use PHPUnit\Framework\TestCase;

class ScalarResolverTest extends TestCase {

  public function testResolvesStrings() {
    $resolver = new ScalarResolver();
    $this->assertTrue($resolver->resolves('string'));
    $this->assertSame('5', $resolver->resolve('string',5));
  }

  public function testResolvesInts() {
    $resolver = new ScalarResolver();
    $this->assertTrue($resolver->resolves('integer'));
    $this->assertSame(5, $resolver->resolve('integer','5'));
  }

  public function testResolvesBooleans() {
    $resolver = new ScalarResolver();
    $this->assertTrue($resolver->resolves('boolean'));
    $this->assertSame(TRUE, $resolver->resolve('boolean', 1));
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\InvalidVariableException
   * @expectedExceptionMessage Invalid type unknown passed to LastCall\Mannequin\Core\Variable\ScalarResolver
   */
  public function testFailsOnInvalidType() {
    $resolver = new ScalarResolver();
    $this->assertFalse($resolver->resolves('unknown'));
    $resolver->resolve('unknown', 'foo');
  }

}