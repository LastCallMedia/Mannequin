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
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class CommentTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $comment = $this->parser->getStream()->expect(Token::TEXT_TYPE)->getValue();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new Comment($comment, $token->getLine());
    }

    public function getTag()
    {
        return 'comment';
    }
}
