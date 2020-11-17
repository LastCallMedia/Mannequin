<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Twig\NodeVisitor;

use LastCall\Mannequin\Twig\Twig\Node\Comment;
use Twig\Environment;
use Twig\Node\TextNode;
use Twig\Node\BlockNode;
use Twig\Node\Node;
use Twig\Node\ModuleNode;
use Twig\Error\SyntaxError;

/**
 * This visitor searches through comment nodes looking for @Component
 * annotations.
 *
 * When annotations are found, it extracts the content into a "componentinfo"
 * block that can be rendered separately from the rest of the template.
 */
class ComponentInfoNodeVisitor implements \Twig\NodeVisitor\NodeVisitorInterface
{
    const INFO_BLOCK = 'componentinfo';
    private $info;

    public function enterNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {
            $this->info = null;
        }
        if ($this->isComponentInfo($node)) {
            if ($this->info) {
                throw new SyntaxError('Multiple component info blocks were detected.');
            }
            $this->info = $node;
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {
            $blocks = $node->getNode('blocks');
            // BC with 1.0.0: Do not override an existing info block.
            if ($blocks->hasNode(self::INFO_BLOCK)) {
                @trigger_error(sprintf('Setting an explicit %s block is deprecated as of 1.1.0 due to incompatibilities with Twig inheritance.  This feature will be removed soon.  You should use the new comment syntax instead.', self::INFO_BLOCK), E_USER_DEPRECATED);
            } else {
                $node->getNode('blocks')->setNode(self::INFO_BLOCK, $this->getInfoBlock());
            }
        }

        return $node;
    }

    private function isComponentInfo(\Twig_NodeInterface $node)
    {
        if ($node instanceof Comment) {
            $comment = $node->getAttribute('data');

            return preg_match('/^\s*\@[Cc]omponent\s+/', $comment);
        }
    }

    protected function getComponentInfo(string $comment)
    {
        return preg_replace('/^\s*\@[Cc]omponent\s+/', '', $comment);
    }

    private function getInfoBlock()
    {
        if ($this->info) {
            $comment = $this->getComponentInfo($this->info->getAttribute('data'));
            $nodes = [new TextNode($this->getComponentInfo($comment), $this->info->getTemplateLine())];
        } else {
            $nodes = [];
        }

        return new BlockNode(
            'componentinfo',
            new Node($nodes),
            0
        );
    }

    public function getPriority()
    {
        return 0;
    }
}
