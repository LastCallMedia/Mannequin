<?php

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\YamlMetadataParser;

trait YamlParserProphecyTrait
{
    public function getParserProphecy(array $partialMetadata)
    {
        $metadata = $partialMetadata + [
                'name' => '',
                'description' => '',
                'tags' => [],
                'definition' => new Definition(),
                'sets' => [],
            ];
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse('')->willreturn($metadata);

        return $parser;
    }

    abstract public function prophesize($classOrInterface = null);
}
