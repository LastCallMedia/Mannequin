<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\YamlMetadataParser;

trait YamlParserProphecyTrait
{
    public function getParserProphecy(array $partialMetadata, $exceptionIdentifier = 'unknown')
    {
        $metadata = $partialMetadata + [
                'name' => '',
                'description' => '',
                'tags' => [],
                'sets' => [],
            ];
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse('', $exceptionIdentifier)->willreturn($metadata);

        return $parser;
    }

    abstract public function prophesize($classOrInterface = null);
}
