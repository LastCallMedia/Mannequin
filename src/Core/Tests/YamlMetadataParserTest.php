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
        $parsed = (new YamlMetadataParser())->parse('description: foo');
        $this->assertArraySubset(['description' => 'foo'], $parsed['tags']);
    }

    public function testParsesGroup()
    {
        $parsed = (new YamlMetadataParser())->parse('group: foo');
        $this->assertArraySubset(['group' => 'foo'], $parsed['tags']);
    }

    public function testDefaults()
    {
        $parsed = (new YamlMetadataParser())->parse('{}');
        $this->assertEquals('', $parsed['name']);
        $this->assertEquals([], $parsed['tags']);
        $this->assertEquals([], $parsed['samples']);
    }

    public function testParsesTags()
    {
        $parsed = (new YamlMetadataParser())->parse('_foo: bar');
        $this->assertEquals(['foo' => 'bar'], $parsed['tags']);
    }

    public function testParsesSamples()
    {
        $parsed = (new YamlMetadataParser())->parse(
            'samples:
                    foo: {bar: baz, _baz: bar}
                    bar: {_name: Barz}'
        );
        $this->assertInternalType('array', $parsed['samples']['foo']);

        return $parsed;
    }

    /**
     * @depends testParsesSamples
     */
    public function testParsesSampleMetadata($metadata)
    {
        $this->assertEquals(['baz' => 'bar'], $metadata['samples']['foo']['tags']);
    }

    /**
     * @depends testParsesSamples
     */
    public function testParsesSampleNameFromTags($metadata)
    {
        $this->assertEquals('Barz', $metadata['samples']['bar']['name']);
    }

    /**
     * @depends testParsesSamples
     */
    public function testRemovesSampleNameFromTags($metadata)
    {
        $this->assertArrayNotHasKey('name', $metadata['samples']['bar']['tags']);
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
                'samples: ""',
                'foo',
                new TemplateParsingException('samples must be an array in foo'),
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
