<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ManifestBuilder
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function generate(PatternCollection $collection)
    {
        $manifest = ['patterns' => []];
        $generator = $this->generator;
        foreach ($collection as $pattern) {
            $id = $pattern->getId();
            $manifest['patterns'][] = [
                'id' => $id,
                'name' => $pattern->getName(),
                'problems' => $pattern->getProblems(),
                'source' => $generator->generate(
                    'pattern_render_source_raw',
                    ['pattern' => $id],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
                'tags' => $pattern->getTags(),
                'variants' => $this->generateVariants($pattern),
                'used' => $this->generateUsed($pattern),
                'aliases' => $pattern->getAliases(),
            ];
        }

        return $manifest;
    }

    private function generateVariants(PatternInterface $pattern)
    {
        $variants = [];
        $generator = $this->generator;
        foreach ($pattern->getVariants() as $id => $variant) {
            $variants[] = [
                'id' => $variant->getId(),
                'name' => $variant->getName(),
                'tags' => $variant->getTags(),
                'source' => $generator->generate(
                    'pattern_render_raw',
                    ['pattern' => $pattern->getId(), 'variant' => $id],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
                'rendered' => $generator->generate(
                    'pattern_render',
                    ['pattern' => $pattern->getId(), 'variant' => $id],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
            ];
        }

        return $variants;
    }

    private function generateUsed(PatternInterface $pattern)
    {
        return array_map(
            function (PatternInterface $used) {
                return $used->getId();
            },
            $pattern->getUsedPatterns()
        );
    }
}
