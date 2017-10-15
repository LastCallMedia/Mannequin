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

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Twig\Component\TwigComponent;

class InlineTwigYamlMetadataSubscriber extends YamlFileMetadataSubscriber
{
    const BLOCK_NAME = 'componentinfo';

    protected function getMetadataForComponent(ComponentInterface $component)
    {
        if ($component instanceof TwigComponent) {
            try {
                $template = $component->getTwig()->load($component->getSource()->getName());
                if ($template->hasBlock(self::BLOCK_NAME)) {
                    $yaml = $template->renderBlock(self::BLOCK_NAME);
                    if (!empty($yaml)) {
                        return $this->parseYaml($yaml, $component->getSource()->getName());
                    }
                }
            } catch (\Twig_Error $e) {
                $message = sprintf('Twig error thrown during componentinfo generation of %s: %s',
                    $component->getSource()->getName(),
                    $e->getMessage()
                );
                throw new TemplateParsingException($message, $e->getCode(), $e);
            }
        }
    }
}
