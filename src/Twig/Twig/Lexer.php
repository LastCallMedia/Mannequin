<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Twig;

use Twig\Token;

/**
 * Lexer overrides the default lexer to provide parsing of comments into
 * Comment nodes.
 *
 * @see \LastCall\Mannequin\Twig\Twig\TokenParser\CommentTokenParser
 *
 * Comments are processed later into `componentinfo` blocks.
 * @see \LastCall\Mannequin\Twig\Twig\NodeVisitor\ComponentInfoNodeVisitor
 */
class Lexer extends \Twig\Lexer
{
    private $tokens;
    private $code;
    private $cursor;
    private $lineno;
    private $regexes;

    /**
     * Override of \Twig_Lexer::lexComment().
     *
     * Extract the comment into the token stream, then pass control back to the
     * default lexer.
     */
    private function lexComment()
    {
        if (preg_match($this->regexes['lex_comment'], $this->code, $match, PREG_OFFSET_CAPTURE, $this->cursor)) {
            $comment = substr($this->code, $this->cursor, $match[0][1] - $this->cursor);
            $this->pushToken(Token::BLOCK_START_TYPE);
            $this->pushToken(Token::NAME_TYPE, 'comment');
            $this->pushToken(Token::TEXT_TYPE, $comment);
            $this->pushToken(Token::BLOCK_END_TYPE);
        }

        // Pass back to the normal lexer to skip past the comment.
        return parent::lexComment();
    }

    // In Drupal 8.9 this protect function and Drupal 9.0 its private function.
    protected function pushToken($type, $value = '')
    {
        // do not push empty text tokens
        if (/* Token::TEXT_TYPE */ 0 === $type && '' === $value) {
            return;
        }

        $this->tokens[] = new Token($type, $value, $this->lineno);
    }
}
