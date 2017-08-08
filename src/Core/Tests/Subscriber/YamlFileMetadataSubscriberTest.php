<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Subscriber;

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\PatternVariant;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\Tests\Stubs\TestFilePattern;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\Variable\Variable;
use LastCall\Mannequin\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class YamlFileMetadataSubscriberTest extends TestCase
{
    use DiscoverySubscriberTestTrait;
    use YamlParserProphecyTrait;

    private $templateFile;
    private $yamlFile;

    public function setUp()
    {
        parent::setUp();
        $this->yamlFile = sys_get_temp_dir().'/test.yml';
        $this->templateFile = sys_get_temp_dir().'/test.html';
        (new Filesystem())->touch($this->yamlFile);
    }

    public function testParsesMetadata()
    {
        $parser = $this->getParserProphecy(
            [
                'name' => 'Foo',
                'tags' => ['foo' => 'bar'],
                'variants' => [
                    'additional' => [
                        'name' => 'Additional',
                        'variables' => new VariableSet([
                            'var1' => new Variable('simple', 'foo'),
                        ]),
                    ],
                ],
            ],
            $this->yamlFile
        );
        $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );
        $this->assertInstanceOf(TestFilePattern::class, $event->getPattern());

        return $event->getPattern();
    }

    /**
     * @depends testParsesMetadata
     */
    public function testSetsPatternName(TestFilePattern $pattern)
    {
        $this->assertEquals('Foo', $pattern->getName());
    }

    /**
     * @depends testParsesMetadata
     */
    public function testSetsPatternTags(TestFilePattern $pattern)
    {
        $this->assertArraySubset(['foo' => 'bar'], $pattern->getMetadata());
    }

    /**
     * @depends testParsesMetadata
     */
    public function testAddsVariant(TestFilePattern $pattern)
    {
        $this->assertTrue($pattern->hasVariant('additional'));
        $expectedVariant = new PatternVariant(
            'additional',
            'Additional',
            new VariableSet(['var1' => new Variable('simple', 'foo')])
        );
        $this->assertEquals($expectedVariant, $pattern->getVariant('additional'));
    }

    public function testParsesOverrideMetadata()
    {
        $parser = $this->getParserProphecy(
            [
                'name' => 'Foo',
                'tags' => ['foo' => 'baz'],
                'variants' => [
                    'default' => [
                        'name' => 'Overridden',
                        'variables' => new VariableSet([
                            'var1' => new Variable('simple', 'foo'),
                        ]),
                    ],
                ],
            ],
            $this->yamlFile
        );
        $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
        $pattern->addMetadata('foo', 'bar');
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );
        $this->assertInstanceOf(TestFilePattern::class, $event->getPattern());

        return $event->getPattern();
    }

    /**
     * @depends testParsesOverrideMetadata
     */
    public function testOverridesTags(PatternInterface $pattern)
    {
        $this->assertArraySubset([
            'foo' => 'baz',
        ], $pattern->getMetadata());
    }

    /**
     * @depends testParsesOverrideMetadata
     */
    public function testOverridesDefaultVariant(PatternInterface $pattern)
    {
        $expectedVariant = new PatternVariant(
            'default',
            'Overridden',
            new VariableSet(['var1' => new Variable('simple', 'foo')])
        );
        $this->assertEquals($expectedVariant, $pattern->getVariant('default'));
    }
}
