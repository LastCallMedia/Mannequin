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
use LastCall\Mannequin\Core\Variable\Definition;
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
            'description' => '',
            'group' => '',
            'tags' => [],
            'variables' => [],
            'value' => [],
            'values' => [],
        ];
        foreach (['name', 'description', 'group'] as $component) {
            if (!is_string($metadata[$component])) {
                throw new TemplateParsingException(
                    sprintf(
                        '%s must be a string in %s',
                        $component,
                        $exceptionIdentifier
                    )
                );
            }
        }
        foreach (['tags', 'variables', 'value', 'values'] as $component) {
            if (!is_array($metadata[$component])) {
                throw new TemplateParsingException(
                    sprintf(
                        '%s must be an array in %s',
                        $component,
                        $exceptionIdentifier
                    )
                );
            }
        }
        $metadata['definition'] = $this->createDefinition(
            $metadata,
            $exceptionIdentifier
        );
        $metadata['sets'] = $this->createSets($metadata, $exceptionIdentifier);

        return $metadata;
    }

    public function createDefinition(array $metadata, $exceptionIdentifier)
    {
        $definition = [];
        foreach ($metadata['variables'] as $name => $type) {
            $definition[$name] = $type;
        }

        return new Definition($definition);
    }

    public function createSets(array $metadata, $exceptionIdentifier)
    {
        $sets = [];
        if (!empty($metadata['value'])) {
            $sets['default'] = $this->createSet($metadata['value'], 'Default');
        }
        if (!empty($metadata['values'])) {
            foreach ($metadata['values'] as $setId => $setVals) {
                $sets[$setId] = $this->createSet($setVals, $setId);
            }
        }

        return $sets;
    }

    public function createSet($values, $defaultName)
    {
        $name = $defaultName;
        $description = '';
        if (isset($values['_name'])) {
            $name = $values['_name'];
            unset($values['_name']);
        }
        if (isset($values['_description'])) {
            $description = $values['_description'];
            unset($values['_description']);
        }

        return new Set($name, $values, $description);
    }
}
