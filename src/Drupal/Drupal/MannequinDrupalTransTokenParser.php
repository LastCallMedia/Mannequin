<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Drupal;

use Drupal\Core\Template\TwigTransTokenParser;
use Twig\Node\Expression\Binary\GreaterBinary;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IfNode;
use Twig\Node\Node;
use Twig\Token;

/**
 * Parser to nullify trans tokens.
 */
class MannequinDrupalTransTokenParser extends TwigTransTokenParser
{
    public function parse(Token $token)
    {
        $node = parent::parse($token);

        $lineno = $node->getTemplateLine();

        // Convert plural into a simple if/else statement.
        if ($node->hasNode('plural') && $node->hasNode('count')) {
            return new IfNode(
              new Node([
                  new GreaterBinary(
                    $node->getNode('count'),
                    new ConstantExpression(1, $lineno),
                    $lineno
                  ),
                  $node->getNode('plural')
                ]
              ),
              $node->getNode('body'),
              $lineno
            );
        }
        return $node->getNode('body');
    }
}
