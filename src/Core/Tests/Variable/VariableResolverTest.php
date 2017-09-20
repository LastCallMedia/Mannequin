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

use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Variable\Variable;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class VariableResolverTest extends TestCase
{
    public function getResolveTests()
    {
        return [
            [new Variable('simple', 'foo'), 'foo'],
            [new Variable('expression', 'constant("PHP_VERSION")'), PHP_VERSION],
        ];
    }

    /**
     * @dataProvider getResolveTests
     */
    public function testResolve($input, $expected)
    {
        $el = new ExpressionLanguage();
        $mannequin = $this->prophesize(Mannequin::class);
        $resolver = new VariableResolver($el, $mannequin->reveal());
        $output = $resolver->resolve($input);
        $this->assertEquals($expected, $output);
    }

    public function testResolverAddsMannequinToContext()
    {
        $el = $this->prophesize(ExpressionLanguage::class);
        $mannequin = $this->prophesize(Mannequin::class);
        $el
            ->evaluate('foo', Argument::withEntry('mannequin', $mannequin))
            ->shouldBeCalled();
        $resolver = new VariableResolver($el->reveal(), $mannequin->reveal());
        $resolver->resolve(new Variable('expression', 'foo'));
    }
}
