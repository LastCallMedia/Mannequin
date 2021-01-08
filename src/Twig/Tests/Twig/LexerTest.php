<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Twig;

use PHPUnit\Framework\TestCase;
use LastCall\Mannequin\Twig\Twig\Lexer;
use Twig\Environment;
use Twig\Source;
use Twig\Token;

class LexerTest extends TestCase
{
    public function testLexComment()
    {
        $template = '{# foo #}';

        $lexer = new Lexer(new Environment($this->getMockBuilder('Twig_LoaderInterface')->getMock()));
        $stream = $lexer->tokenize(new Source($template, 'index'));
        $stream->expect(Token::BLOCK_START_TYPE);
        $stream->expect(Token::NAME_TYPE, 'comment');
        $stream->expect(Token::TEXT_TYPE, ' foo ');
        $stream->expect(Token::BLOCK_END_TYPE);

        // add a dummy assertion here to satisfy PHPUnit, the only thing we want to test is that the code above
        // can be executed without throwing any exceptions
        $this->addToAssertionCount(1);
    }
}
