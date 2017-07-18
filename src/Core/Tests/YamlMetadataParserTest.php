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

use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\YamlMetadataParser;
use PHPUnit\Framework\TestCase;

class YamlMetadataParserTest extends TestCase
{
    public function testParsesName()
    {
        $parsed = (new YamlMetadataParser())->parse('name: foo');
        $this->assertEquals('foo', $parsed['name']);
    }

    public function testParsesDescription()
    {
        $parsed = (new YamlMetadataParser())->parse('_description: foo');
        $this->assertArraySubset(['description' => 'foo'], $parsed['tags']);
    }

    public function testDefaults()
    {
        $parsed = (new YamlMetadataParser())->parse('{}');
        $this->assertEquals('', $parsed['name']);
        $this->assertEquals('', $parsed['description']);
        $this->assertEquals([], $parsed['tags']);
        $this->assertNull($parsed['definition']);
        $this->assertEquals([], $parsed['variants']);
    }

    public function testParsesTags()
    {
        $parsed = (new YamlMetadataParser())->parse('_foo: bar');
        $this->assertEquals(['foo' => 'bar'], $parsed['tags']);
    }

    public function testParsesDefinition()
    {
        $parsed = (new YamlMetadataParser())->parse('variables: {foo: bar}');
        $this->assertEquals(
            ['foo' => 'bar'],
            $parsed['variables']
        );
    }

    public function testParsesVariants()
    {
        $parsed = (new YamlMetadataParser())->parse(
            'variants: {foo: {bar: baz, _baz: bar}}'
        );
        $this->assertInternalType('array', $parsed['variants']['foo']);

        return $parsed;

        $this->assertEquals(
            ['foo' => new Set('foo', ['bar' => 'baz'])],
            $parsed['sets']
        );
    }

    /**
     * @depends testParsesVariants
     */
    public function testParsesVariantValues($metadata)
    {
        $this->assertEquals(['bar' => 'baz'], $metadata['variants']['foo']['values']);
    }

    /**
     * @depends testParsesVariants
     */
    public function testParsesVariantTags($metadata)
    {
        $this->assertEquals(['baz' => 'bar'], $metadata['variants']['foo']['tags']);
    }

    public function getInvalidMetadataTests()
    {
        return [
            [
                '{,]',
                'foo',
                new TemplateParsingException(
                    'Unable to parse YAML metadata in foo. Malformed inline YAML string'
                ),
            ],
            [
                'bar',
                'foo',
                new TemplateParsingException('Metadata must be an array in foo'),
            ],
            [
                'name: {}',
                'foo',
                new TemplateParsingException('name must be a string in foo'),
            ],
            [
                'variables: ""',
                'foo',
                new TemplateParsingException(
                    'variables must be an array in foo'
                ),
            ],
            [
                'variants: ""',
                'foo',
                new TemplateParsingException('variants must be an array in foo'),
            ],
        ];
    }

    /**
     * @dataProvider getInvalidMetadataTests
     */
    public function testInvalidMetadata(
        $input,
        $identifier,
        \Exception $expectedException
    ) {
        try {
            (new YamlMetadataParser())->parse($input, $identifier);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(get_class($expectedException), $e);
            $this->assertContains(
                $expectedException->getMessage(),
                $e->getMessage()
            );

            return;
        }
        $this->fail(sprintf('Expected parse failure with %s', $input));
    }
}
