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

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\TemplateFileInterface;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Variable\VariableSet;
use LastCall\Mannequin\Core\YamlMetadataParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YamlFileMetadataSubscriber implements EventSubscriberInterface
{
    private $parser;

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
            if (!empty($metadata['tags'])) {
                foreach ($metadata['tags'] as $k => $v) {
                    $pattern->addMetadata($k, $v);
                }
            }
            if (!empty($metadata['variants'])) {
                foreach ($metadata['variants'] as $vidx => $setDef) {
                    $name = $setDef['name'] ?? $vidx;
                    $tags = $setDef['tags'] ?? [];
                    $variables = $setDef['variables'] ?? new VariableSet();
                    $pattern->createVariant($vidx, $name, $variables, $tags);
                }
            } else {
                $pattern->createVariant('default', 'Default', new VariableSet(), []);
            }
        }
    }

    protected function getMetadataForPattern(ComponentInterface $pattern)
    {
        if ($pattern instanceof TemplateFileInterface) {
            if ($file = $pattern->getFile()) {
                $yamlFile = $this->getYamlFileForPatternFile($pattern->getFile());
                if (file_exists($yamlFile)) {
                    return $this->parseYaml(file_get_contents($yamlFile), $yamlFile);
                }
            }
        }

        return false;
    }

    protected function parseYaml($yamlString, $exceptionIdentifier = 'unknown')
    {
        return $this->parser->parse($yamlString, $exceptionIdentifier);
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
