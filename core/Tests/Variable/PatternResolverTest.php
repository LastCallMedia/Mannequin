<?php


namespace LastCall\Mannequin\Core\Tests\Variable;

use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Variable\PatternResolver;
use PHPUnit\Framework\TestCase;

class PatternResolverTest extends TestCase {

  public function testResolvesPatternByCallback() {
    $rendered = new Rendered();
    $cb = function($arg) use ($rendered) {
      $this->assertEquals('foo', $arg);
      return $rendered;
    };

    $resolver = new PatternResolver($cb);
    $this->assertSame($rendered, $resolver->resolve('pattern', 'foo'));
  }

  public function testResolvesPatternTypes() {
    $resolver = new PatternResolver(function() {});
    $this->assertTrue($resolver->resolves('pattern'));
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\InvalidVariableException
   * @expectedExceptionMessage Invalid type unknown passed to LastCall\Mannequin\Core\Variable\PatternResolver
   */
  public function testDoesNotResolveOtherTypes() {
    $resolver = new PatternResolver(function() {});
    $this->assertFalse($resolver->resolves('unknown'));
    $resolver->resolve('unknown', 'foo');
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage Pattern resolver callback did not return a valid value for foo
   */
  public function testRequiresRenderedResult() {
    $resolver = new PatternResolver(function() {});
    $this->assertFalse($resolver->resolves('unknown'));
    $resolver->resolve('pattern', 'foo');
  }
}