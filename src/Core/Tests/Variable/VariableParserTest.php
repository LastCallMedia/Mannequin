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

use LastCall\Mannequin\Core\Variable\Variable;
use LastCall\Mannequin\Core\Variable\VariableParser;
use LastCall\Mannequin\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;

class VariableParserTest extends TestCase
{
    public function getParseTests()
    {
        return [
            ['foo', new Variable('simple', 'foo')],
            ['pattern("foo")', new Variable('simple', 'pattern("foo")')],
            ['~pattern("foo")', new Variable('expression', 'pattern("foo")')],
            [['foo' => 'bar'], new VariableSet(['foo' => new Variable('simple', 'bar')])],
            [['foo' => '~pattern("foo")'], new VariableSet(['foo' => new Variable('expression', 'pattern("foo")')])],
        ];
    }

    /**
     * @dataProvider getParseTests
     */
    public function testParse($input, $expected)
    {
        $output = (new VariableParser())->parse($input);
        $this->assertEquals($expected, $output);
    }
}
