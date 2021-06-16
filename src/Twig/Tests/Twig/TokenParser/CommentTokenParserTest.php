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

class CommentTokenParserTest extends TestCase
{
    public function testCommentParsing()
    {
        $twig = new \Twig_Environment($this->getMockBuilder('Twig_LoaderInterface')->getMock(), array(
            'autoescape' => false,
            'optimizations' => 0,
        ));
        $twig->addTokenParser(new CommentTokenParser());

        $parser = new \Twig_Parser($twig);

        $module = $parser->parse(new \Twig_TokenStream(array(
            new \Twig_Token(\Twig_Token::BLOCK_START_TYPE, '', 1),
            new \Twig_Token(\Twig_Token::NAME_TYPE, 'comment', 1),
            new \Twig_Token(\Twig_Token::TEXT_TYPE, 'foo', 1),
            new \Twig_Token(\Twig_Token::BLOCK_END_TYPE, '', 1),
            new \Twig_Token(\Twig_Token::EOF_TYPE, '', 1),
        )));

        $this->assertEquals(
          "Twig\Node\BodyNode(\n  0: LastCall\Mannequin\Twig\Twig\Node\Comment(data: 'foo')\n)",
          (string) $module->getNode('body')
        );
    }
}
