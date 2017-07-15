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
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
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
        if ($pattern instanceof TemplateFilePatternInterface && $file = $pattern->getFile(
            )
        ) {
            $yamlFile = $this->getYamlFileForPatternFile($pattern->getFile());
            if (file_exists($yamlFile)) {
                $metadata = $this->parser->parse(file_get_contents($yamlFile));
                if (!empty($metadata['name'])) {
                    $pattern->setName($metadata['name']);
                }
                if (!empty($metadata['description'])) {
                    $pattern->setDescription($metadata['description']);
                }
                $pattern->setVariableDefinition($metadata['definition']);
                foreach ($metadata['tags'] as $k => $v) {
                    $pattern->addTag($k, $v);
                }
                foreach ($metadata['sets'] as $k => $v) {
                    $pattern->addVariableSet($k, $v);
                }
            }
        }
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
