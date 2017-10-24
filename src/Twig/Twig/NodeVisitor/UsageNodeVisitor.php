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

use Twig_Environment;
use Twig_NodeInterface;

/**
 * Collects data about external template usage via include, embed, and extend
 * tags.
 *
 * This class JSON encodes any static references to external templates into the
 * _collected_usage block.  This block is then cached with the template and is
 * parseable further down the chain.
 */
class UsageNodeVisitor implements \Twig_NodeVisitorInterface
{
    private $collected = [];

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Module) {
            $this->collected = [];
        }

        // Collect embeds.
        if ($node->hasAttribute('embedded_templates')) {
            foreach ($node->getAttribute('embedded_templates') as $embed_node) {
                if ($embed_node->hasNode('parent')) {
                    $value = $this->getResolvableValue($embed_node->getNode('parent'));
                    if (false !== $value) {
                        $this->collected[] = $value;
                    }
                }
            }
        }

        // Collect extends.
        if ($node->hasNode('parent')) {
            $value = $this->getResolvableValue($node->getNode('parent'));
            if (false !== $value) {
                $this->collected[] = $value;
            }
        }

        // Collect includes.
        if ($node instanceof \Twig_Node_Include) {
            $value = $this->getResolvableValue($node->getNode('expr'));

            if (false !== $value) {
                $this->collected[] = $value;
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Module) {
            $node->getNode('blocks')->setNode('_collected_usage', $this->getCollectedIncludesBlock($this->collected));
        }

        return $node;
    }

    /**
     * Check an expression node to be sure it is a constant value we can resolve
     * at compile time.
     *
     * @param \Twig_Node $node
     *
     * @return string|false
     */
    private function getResolvableValue(\Twig_Node_Expression $node)
    {
        if ($node instanceof \Twig_Node_Expression_Constant
            && 'not_used' !== $node->getAttribute('value')) {
            return $node->getAttribute('value');
        }

        return false;
    }

    /**
     * Formulate a block that returns a JSON encoded version of the included
     * templates.
     *
     * @param array $includes
     *
     * @return \Twig_Node_Block
     */
    private function getCollectedIncludesBlock(array $includes)
    {
        return new \Twig_Node_Block(
            '_collected_usage',
            new \Twig_Node([
                new \Twig_Node_Text(json_encode($includes), 0),
            ]),
            0
        );
    }
}
