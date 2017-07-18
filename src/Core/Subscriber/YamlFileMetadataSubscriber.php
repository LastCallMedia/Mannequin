<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Subscriber;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\YamlMetadataParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YamlFileMetadataSubscriber implements EventSubscriberInterface
{
    public function __construct(YamlMetadataParser $parser = null)
    {
        $this->parser = $parser ?: new YamlMetadataParser();
    }

    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::DISCOVER => 'addYamlMetadata',
        ];
    }

    public function addYamlMetadata(PatternDiscoveryEvent $event)
    {
        $pattern = $event->getPattern();
        if ($metadata = $this->getMetadataForPattern($pattern)) {
            if (!empty($metadata['name'])) {
                $pattern->setName($metadata['name']);
            }
            if (!empty($metadata['variables'])) {
                $pattern->setVariableDefinition(new Definition($metadata['variables']));
            }
            if (!empty($metadata['tags'])) {
                foreach ($metadata['tags'] as $k => $v) {
                    $pattern->addTag($k, $v);
                }
            }
            if (!empty($metadata['variants'])) {
                foreach ($metadata['variants'] as $vidx => $setDef) {
                    $name = $setDef['name'] ?? $vidx;
                    $tags = $setDef['tags'] ?? [];
                    $values = $setDef['values'] ?? [];
                    $pattern->createVariant($vidx, $name, $values, $tags);
                }
            } else {
                $pattern->createVariant('default', 'Default', [], []);
            }
        }
    }

    protected function getMetadataForPattern(PatternInterface $pattern)
    {
        if ($pattern instanceof TemplateFilePatternInterface) {
            if ($file = $pattern->getFile()) {
                $yamlFile = $this->getYamlFileForPatternFile($pattern->getFile());
                if (file_exists($yamlFile)) {
                    $metadata = $this->parser->parse(file_get_contents($yamlFile));

                    return $metadata;
                }
            }
        }

        return false;
    }

    private function getYamlFileForPatternFile(\SplFileInfo $patternFile)
    {
        $path = $patternFile->getPath();
        $basename = $patternFile->getBasename(
                '.'.$patternFile->getExtension()
            ).'.yml';

        return sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $basename);
    }
}
