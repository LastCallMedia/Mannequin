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
use Twig\Extension\InitRuntimeInterface;
use Twig_Environment;

class MannequinExtension extends \Twig_Extension implements InitRuntimeInterface
{
    public function initRuntime(Twig_Environment $environment)
    {
        $environment->setLexer(new Lexer($environment));
    }

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
