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

class LexerTest extends TestCase
{
    public function testLexComment()
    {
        $template = '{# foo #}';

        $lexer = new Lexer(new \Twig_Environment($this->getMockBuilder('Twig_LoaderInterface')->getMock()));
        $stream = $lexer->tokenize(new \Twig_Source($template, 'index'));
        $stream->expect(\Twig_Token::BLOCK_START_TYPE);
        $stream->expect(\Twig_Token::NAME_TYPE, 'comment');
        $stream->expect(\Twig_Token::TEXT_TYPE, ' foo ');
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        // add a dummy assertion here to satisfy PHPUnit, the only thing we want to test is that the code above
        // can be executed without throwing any exceptions
        $this->addToAssertionCount(1);
    }
}
