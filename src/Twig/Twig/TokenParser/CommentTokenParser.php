<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Twig\TokenParser;

use LastCall\Mannequin\Twig\Twig\Node\Comment;
use Twig_Token;

class CommentTokenParser extends \Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $comment = $this->parser->getStream()->expect(Twig_Token::TEXT_TYPE)->getValue();
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        return new Comment($comment, $token->getLine());
    }

    public function getTag()
    {
        return 'comment';
    }
}
