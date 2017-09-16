<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Subscriber;

use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class InlineTwigYamlMetadataSubscriber extends YamlFileMetadataSubscriber
{
    const BLOCK_NAME = 'patterninfo';

    protected function getMetadataForPattern(PatternInterface $pattern)
    {
        if ($pattern instanceof TwigPattern) {
            try {
                $template = $pattern->getTwig()->load($pattern->getSource()->getName());
                if ($template->hasBlock(self::BLOCK_NAME)) {
                    $yaml = $template->renderBlock(self::BLOCK_NAME);

                    return $this->parseYaml($yaml, $pattern->getSource()->getName());
                }
            } catch (\Twig_Error $e) {
                $message = sprintf('Twig error thrown during patterninfo generation of %s: %s',
                    $pattern->getSource()->getName(),
                    $e->getMessage()
                );
                throw new TemplateParsingException($message, $e->getCode(), $e);
            }
        }
    }
}
