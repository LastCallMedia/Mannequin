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

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\TwigInspectorInterface;

class InlineTwigYamlMetadataSubscriber extends YamlFileMetadataSubscriber
{
    private $inspector;

    public function __construct(
        TwigInspectorInterface $inspector,
        YamlMetadataParser $parser = null
    ) {
        $this->inspector = $inspector;
        parent::__construct($parser);
    }

    protected function getMetadataForPattern(PatternInterface $pattern)
    {
        if ($pattern instanceof TwigPattern) {
            $yaml = $this->inspector->inspectPatternData($pattern->getSource());
            if (false !== $yaml) {
                return $this->parseYaml($yaml, $pattern->getSource()->getPath());
            }
        }
    }
}
