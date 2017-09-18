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
use LastCall\Mannequin\Core\Event\ComponentDiscoveryEvent;
use LastCall\Mannequin\Core\Event\ComponentEvents;
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
            ComponentEvents::DISCOVER => 'addYamlMetadata',
        ];
    }

    public function addYamlMetadata(ComponentDiscoveryEvent $event)
    {
        $component = $event->getComponent();
        if ($metadata = $this->getMetadataForComponent($component)) {
            if (!empty($metadata['name'])) {
                $component->setName($metadata['name']);
            }
            if (!empty($metadata['tags'])) {
                foreach ($metadata['tags'] as $k => $v) {
                    $component->addMetadata($k, $v);
                }
            }
            if (!empty($metadata['variants'])) {
                foreach ($metadata['variants'] as $vidx => $setDef) {
                    $name = $setDef['name'] ?? $vidx;
                    $tags = $setDef['tags'] ?? [];
                    $variables = $setDef['variables'] ?? new VariableSet();
                    $component->createVariant($vidx, $name, $variables, $tags);
                }
            } else {
                $component->createVariant('default', 'Default', new VariableSet(), []);
            }
        }
    }

    protected function getMetadataForComponent(ComponentInterface $component)
    {
        if ($component instanceof TemplateFileInterface) {
            if ($file = $component->getFile()) {
                $yamlFile = $this->getYamlFileForComponentFile($component->getFile());
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

    private function getYamlFileForComponentFile(\SplFileInfo $componentFile)
    {
        $path = $componentFile->getPath();
        $basename = $componentFile->getBasename(
                '.'.$componentFile->getExtension()
            ).'.yml';

        return sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $basename);
    }
}
