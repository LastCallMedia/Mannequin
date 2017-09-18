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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ManifestBuilder
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function generate(ComponentCollection $collection)
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
                'metadata' => $pattern->getMetadata(),
                'variants' => $this->generateVariants($pattern),
                'used' => $this->generateUsed($pattern),
                'aliases' => $pattern->getAliases(),
            ];
        }

        return $manifest;
    }

    private function generateVariants(ComponentInterface $pattern)
    {
        $variants = [];
        $generator = $this->generator;
        foreach ($pattern->getVariants() as $id => $variant) {
            $variants[] = [
                'id' => $variant->getId(),
                'name' => $variant->getName(),
                'metadata' => $variant->getMetadata(),
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

    private function generateUsed(ComponentInterface $pattern)
    {
        return array_map(
            function (ComponentInterface $used) {
                return $used->getId();
            },
            $pattern->getUsedComponents()
        );
    }
}
