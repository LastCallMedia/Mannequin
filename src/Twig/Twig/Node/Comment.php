<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Twig\Node;

/**
 * A comment node represents a lexed Twig comment.
 *
 * @see \LastCall\Mannequin\Twig\Twig\Lexer
 */
class Comment extends \Twig_Node
{
    public function __construct($comment, $lineno = 0)
    {
        parent::__construct([], ['data' => $comment], $lineno);
    }
}
