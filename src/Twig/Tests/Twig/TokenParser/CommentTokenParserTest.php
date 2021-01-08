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

use LastCall\Mannequin\Twig\Twig\TokenParser\CommentTokenParser;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Parser;
use Twig\Token;

class CommentTokenParserTest extends TestCase
{
    public function testCommentParsing()
    {
        $twig = new Environment($this->getMockBuilder('Twig_LoaderInterface')->getMock(), [
            'autoescape' => false,
            'optimizations' => 0,
        ]);
        $twig->addTokenParser(new CommentTokenParser());

        $parser = new Parser($twig);

        $module = $parser->parse(new \Twig_TokenStream([
            new Token(Token::BLOCK_START_TYPE, '', 1),
            new Token(Token::NAME_TYPE, 'comment', 1),
            new Token(Token::TEXT_TYPE, 'foo', 1),
            new Token(Token::BLOCK_END_TYPE, '', 1),
            new Token(Token::EOF_TYPE, '', 1),
        ]));

        $this->assertEquals(
          "Twig\Node\BodyNode(\n  0: LastCall\Mannequin\Twig\Twig\Node\Comment(data: 'foo')\n)",
          (string) $module->getNode('body')
        );
    }
}
