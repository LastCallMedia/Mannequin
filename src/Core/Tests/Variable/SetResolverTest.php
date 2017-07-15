<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Variable;

use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\ResolverInterface;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\SetResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class SetResolverTest extends TestCase
{
    public function testResolvesKnownTypes()
    {
        $set = new Set('Test', ['foo' => 'baz']);
        $definition = new Definition(['foo' => 'bar']);

        $setResolver = new SetResolver([$this->getBarResolver()]);
        $this->assertEquals(
            ['foo' => 'baz'],
            $setResolver->resolveSet($definition, $set)
        );
    }

    protected function getBarResolver()
    {
        $resolver = $this->prophesize(ResolverInterface::class);
        $resolver->resolves(Argument::type('string'))->will(
            function ($args) {
                return $args[0] === 'bar';
            }
        );
        $resolver->resolves('bar')->willReturn(true);
        $resolver->resolve(Argument::type('string'), Argument::any())
            ->willReturnArgument(1);

        return $resolver->reveal();
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\InvalidVariableException
     * @expectedExceptionMessage No resolver knows how to resolve a baz
     *   variable
     */
    public function testDoesNotResolveUnknown()
    {
        $set = new Set('Test', ['foo' => 'baz']);
        $definition = new Definition(['foo' => 'baz']);

        $setResolver = new SetResolver([$this->getBarResolver()]);
        $this->assertEquals(
            ['foo' => 'baz'],
            $setResolver->resolveSet($definition, $set)
        );
    }
}
