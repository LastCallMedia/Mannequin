<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Variable\Set;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlMetadataParser
{
    public function parse($yaml, $exceptionIdentifier = 'unknown')
    {
        try {
            $yaml = Yaml::parse($yaml);
        } catch (ParseException $e) {
            throw new TemplateParsingException(
                sprintf(
                    'Unable to parse YAML metadata in %s. %s',
                    $exceptionIdentifier,
                    $e->getMessage()
                ), $e->getCode(), $e
            );
        }

        return $this->processMetadata($yaml, $exceptionIdentifier);
    }

    private function processMetadata($metadata, $exceptionIdentifier)
    {
        if (!is_array($metadata)) {
            throw new TemplateParsingException(
                sprintf('Metadata must be an array in %s', $exceptionIdentifier)
            );
        }
        $metadata += [
            'name' => '',
            'tags' => [],
            'variables' => [],
            'variants' => [],
        ];

        return [
            'name' => $this->extractName($metadata, $exceptionIdentifier),
            'variables' => $this->extractVariables($metadata, $exceptionIdentifier),
            'tags' => $this->extractTags($metadata),
            'variants' => $this->extractVariants($metadata, $exceptionIdentifier),
        ];
    }

    private function extractVariants(array $metadata, $exceptionIdentifier)
    {
        $metadata += ['variants' => []];
        if (!is_array($metadata['variants'])) {
            throw new TemplateParsingException(
                sprintf(
                    'variants must be an array in %s',
                    $exceptionIdentifier
                )
            );
        }

        $variants = [];
        foreach ($metadata['variants'] as $name => $definition) {
            if (!is_array($definition)) {
                throw new TemplateParsingException(
                    sprintf(
                        'variant %s must be an array in %s',
                        $name,
                        $exceptionIdentifier
                    )
                );
            }
            $tags = $this->extractTags($definition, true);
            // @todo: Pass tags to set.
            $variants[$name] = [
                'name' => $name,
                'values' => $definition,
                'tags' => $tags,
            ];
        }

        return $variants;
    }

    private function extractVariables(array $metadata, $exceptionIdentifier)
    {
        $metadata += ['variables' => []];
        if (!is_array($metadata['variables'])) {
            throw new TemplateParsingException(
                sprintf(
                    'variables must be an array in %s',
                    $exceptionIdentifier
                )
            );
        }

        return $metadata['variables'];
    }

    private function extractName(array $metadata, $exceptionIdentifier)
    {
        $metadata += ['name' => ''];
        if (!is_string($metadata['name'])) {
            throw new TemplateParsingException(
                sprintf(
                    'name must be a string in %s',
                    $exceptionIdentifier
                )
            );
        }

        return $metadata['name'];
    }

    public function extractTags(array &$metadata, $remove = false)
    {
        $tags = [];
        foreach ($metadata as $k => $v) {
            if (strpos($k, '_') === 0) {
                $tags[substr($k, 1)] = $v;
                if ($remove) {
                    unset($metadata[$k]);
                }
            }
        }

        return $tags;
    }
}
