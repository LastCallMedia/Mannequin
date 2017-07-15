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
        $parsed = (new YamlMetadataParser())->parse('description: foo');
        $this->assertEquals('foo', $parsed['description']);
    }

    public function testDefaults()
    {
        $parsed = (new YamlMetadataParser())->parse('{}');
        $this->assertEquals('', $parsed['name']);
        $this->assertEquals('', $parsed['description']);
        $this->assertEquals([], $parsed['tags']);
        $this->assertEquals(new Definition(), $parsed['definition']);
        // @todo: Fix this once the format is updated.
        $this->assertEquals([], $parsed['sets']);
    }

    public function testParsesTags()
    {
        $parsed = (new YamlMetadataParser())->parse('tags: {foo: bar}');
        $this->assertEquals(['foo' => 'bar'], $parsed['tags']);
    }

    public function testParsesDefinition()
    {
        $parsed = (new YamlMetadataParser())->parse('variables: {foo: bar}');
        $this->assertEquals(
            new Definition(['foo' => 'bar']),
            $parsed['definition']
        );
    }

    public function testParsesValueToDefaultSet()
    {
        $parsed = (new YamlMetadataParser())->parse('value: {foo: bar}');
        $this->assertEquals(
            ['default' => new Set('Default', ['foo' => 'bar'])],
            $parsed['sets']
        );
    }

    public function testParsesValuesToDefaultSet()
    {
        $parsed = (new YamlMetadataParser())->parse(
            'values: {foo: {bar: baz}}'
        );
        $this->assertEquals(
            ['foo' => new Set('foo', ['bar' => 'baz'])],
            $parsed['sets']
        );
    }

    public function testParseValueNameToDefaultSet()
    {
        $parsed = (new YamlMetadataParser())->parse(
            'value: {_name: My Default, _description: Some description }'
        );
        $this->assertEquals(
            ['default' => new Set('My Default', [], 'Some description')],
            $parsed['sets']
        );
    }

    public function testParseValuesNameToSetName()
    {
        $parsed = (new YamlMetadataParser())->parse(
            'values: {foo: {_name: My Foo, _description: Some description }}'
        );
        $this->assertEquals(
            ['foo' => new Set('My Foo', [], 'Some description')],
            $parsed['sets']
        );
    }

    public function getInvalidMetadataTests()
    {
        return [
            [
                '{,]',
                'foo',
                new TemplateParsingException(
                    'Unable to parse YAML metadata in foo'
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
                'description: {}',
                'foo',
                new TemplateParsingException(
                    'description must be a string in foo'
                ),
            ],
            [
                'tags: ""',
                'foo',
                new TemplateParsingException('tags must be an array in foo'),
            ],
            [
                'variables: ""',
                'foo',
                new TemplateParsingException(
                    'variables must be an array in foo'
                ),
            ],
            [
                'value: ""',
                'foo',
                new TemplateParsingException('value must be an array in foo'),
            ],
            [
                'values: ""',
                'foo',
                new TemplateParsingException('values must be an array in foo'),
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
            $this->assertEquals(
                $expectedException->getMessage(),
                $e->getMessage()
            );

            return;
        }
        $this->fail(sprintf('Expected parse failure with %s', $input));
    }
}
