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
use LastCall\Mannequin\Core\Variable\VariableParser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlMetadataParser
{
    private $variableParser;

    public function __construct(VariableParser $variableParser = null)
    {
        $this->variableParser = $variableParser ?: new VariableParser();
    }

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

        return $this->process($yaml, $exceptionIdentifier);
    }

    private function process($metadata, $exceptionIdentifier)
    {
        if (!is_array($metadata)) {
            throw new TemplateParsingException(
                sprintf('Metadata must be an array in %s', $exceptionIdentifier)
            );
        }
        $metadata += [
            'name' => '',
            'tags' => [],
            'samples' => [],
        ];

        return [
            'name' => $this->extractName($metadata, $exceptionIdentifier),
            'tags' => $this->extractMetadata($metadata, ['description', 'group']),
            'samples' => $this->extractSamples($metadata, $exceptionIdentifier),
        ];
    }

    private function extractSamples(array $metadata, $exceptionIdentifier)
    {
        $metadata += ['samples' => []];
        if (!is_array($metadata['samples'])) {
            throw new TemplateParsingException(
                sprintf(
                    'samples must be an array in %s',
                    $exceptionIdentifier
                )
            );
        }

        $samples = [];
        foreach ($metadata['samples'] as $key => $definition) {
            if (!is_array($definition)) {
                throw new TemplateParsingException(
                    sprintf(
                        'sample %s must be an array in %s',
                        $key,
                        $exceptionIdentifier
                    )
                );
            }
            $tags = $this->extractMetadata($definition, [],true);

            $name = $key;
            if (isset($tags['name'])) {
                $name = $tags['name'];
                unset($tags['name']);
            }
            $samples[$key] = [
                'name' => $name,
                'tags' => $tags,
                'variables' => $this->variableParser->parse($definition),
            ];
        }

        return $samples;
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

    public function extractMetadata(array &$metadata, array $extraKeys = [], $remove = false)
    {
        $extras = array_flip($extraKeys);
        $tags = [];
        foreach ($metadata as $k => $v) {
            if (strpos($k, '_') === 0) {
                $tags[substr($k, 1)] = $v;
                if ($remove) {
                    unset($metadata[$k]);
                }
            }
            elseif(isset($extras[$k])) {
                $tags[$k] = $v;
            }
        }

        return $tags;
    }
}
