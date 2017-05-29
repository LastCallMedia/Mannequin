<?php


namespace LastCall\Mannequin\Core\Tests\Variable;


use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\ResolverInterface;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\SetResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class SetResolverTest extends TestCase {

  protected function getBarResolver() {
    $resolver = $this->prophesize(ResolverInterface::class);
    $resolver->resolves(Argument::type('string'))->will(function($args) {
      return $args[0] === 'bar';
    });
    $resolver->resolves('bar')->willReturn(TRUE);
    $resolver->resolve(Argument::type('string'), Argument::any())->willReturnArgument(1);
    return $resolver->reveal();
  }

  public function testResolvesKnownTypes() {
    $set = new Set('Test', ['foo' => 'baz']);
    $definition = new Definition(['foo' => 'bar']);

    $setResolver = new SetResolver([$this->getBarResolver()]);
    $this->assertEquals(['foo' => 'baz'], $setResolver->resolveSet($definition, $set));
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\InvalidVariableException
   * @expectedExceptionMessage No resolver knows how to resolve a baz variable
   */
  public function testDoesNotResolveUnknown() {
    $set = new Set('Test', ['foo' => 'baz']);
    $definition = new Definition(['foo' => 'baz']);

    $setResolver = new SetResolver([$this->getBarResolver()]);
    $this->assertEquals(['foo' => 'baz'], $setResolver->resolveSet($definition, $set));
  }
}