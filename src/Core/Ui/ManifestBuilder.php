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
        $manifest = ['components' => []];
        $generator = $this->generator;
        foreach ($collection as $component) {
            $id = $component->getId();
            $manifest['components'][] = [
                'id' => $id,
                'name' => $component->getName(),
                'problems' => $component->getProblems(),
                'source' => $generator->generate(
                    'component_render_source_raw',
                    ['component' => $id],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
                'metadata' => $component->getMetadata(),
                'samples' => $this->generateSamples($component),
                'used' => $this->generateUsed($component),
                'aliases' => $component->getAliases(),
            ];
        }

        return $manifest;
    }

    private function generateSamples(ComponentInterface $component)
    {
        $samples = [];
        $generator = $this->generator;
        foreach ($component->getSamples() as $id => $sample) {
            $samples[] = [
                'id' => $sample->getId(),
                'name' => $sample->getName(),
                'metadata' => $sample->getMetadata(),
                'source' => $generator->generate(
                    'component_render_raw',
                    ['component' => $component->getId(), 'sample' => $id],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
                'rendered' => $generator->generate(
                    'component_render',
                    ['component' => $component->getId(), 'sample' => $id],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
            ];
        }

        return $samples;
    }

    private function generateUsed(ComponentInterface $component)
    {
        return array_map(
            function (ComponentInterface $used) {
                return $used->getId();
            },
            $component->getUsedComponents()
        );
    }
}
