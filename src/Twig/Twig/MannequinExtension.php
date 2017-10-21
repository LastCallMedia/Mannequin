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

use LastCall\Mannequin\Twig\Twig\NodeVisitor\ComponentInfoNodeVisitor;
use LastCall\Mannequin\Twig\Twig\NodeVisitor\UsageNodeVisitor;
use LastCall\Mannequin\Twig\Twig\TokenParser\CommentTokenParser;

/**
 * This Twig Extension must be used in combination with the special Lexer.
 *
 * The Lexer must be added separately, because initRuntime happens after
 * the first template is lexed.
 */
class MannequinExtension extends \Twig_Extension
{
    public function getNodeVisitors()
    {
        return [
            new ComponentInfoNodeVisitor(),
            new UsageNodeVisitor(),
        ];
    }

    public function getTokenParsers()
    {
        return [
            new CommentTokenParser(),
        ];
    }
}
