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

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\Tests\Stubs\TestFileComponent;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\Variable\Variable;
use LastCall\Mannequin\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class YamlFileMetadataSubscriberTest extends TestCase
{
    use ComponentSubscriberTestTrait;
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
                'samples' => [
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
        $component = new TestFileComponent('foo', [], new \SplFileInfo($this->templateFile));
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $component
        );
        $this->assertInstanceOf(TestFileComponent::class, $event->getComponent());

        return $event->getComponent();
    }

    /**
     * @depends testParsesMetadata
     */
    public function testSetsComponentName(TestFileComponent $component)
    {
        $this->assertEquals('Foo', $component->getName());
    }

    /**
     * @depends testParsesMetadata
     */
    public function testSetsComponentTags(TestFileComponent $component)
    {
        $this->assertArraySubset(['foo' => 'bar'], $component->getMetadata());
    }

    /**
     * @depends testParsesMetadata
     */
    public function testAddsSample(TestFileComponent $component)
    {
        $this->assertTrue($component->hasSample('additional'));
        $expectedSample = new Sample(
            'additional',
            'Additional',
            new VariableSet(['var1' => new Variable('simple', 'foo')])
        );
        $this->assertEquals($expectedSample, $component->getSample('additional'));
    }

    public function testParsesOverrideMetadata()
    {
        $parser = $this->getParserProphecy(
            [
                'name' => 'Foo',
                'tags' => ['foo' => 'baz'],
                'samples' => [
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
        $component = new TestFileComponent('foo', [], new \SplFileInfo($this->templateFile));
        $component->addMetadata('foo', 'bar');
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $component
        );
        $this->assertInstanceOf(TestFileComponent::class, $event->getComponent());

        return $event->getComponent();
    }

    /**
     * @depends testParsesOverrideMetadata
     */
    public function testOverridesTags(ComponentInterface $component)
    {
        $this->assertArraySubset([
            'foo' => 'baz',
        ], $component->getMetadata());
    }

    /**
     * @depends testParsesOverrideMetadata
     */
    public function testOverridesDefaultSample(ComponentInterface $component)
    {
        $expectedSample = new Sample(
            'default',
            'Overridden',
            new VariableSet(['var1' => new Variable('simple', 'foo')])
        );
        $this->assertEquals($expectedSample, $component->getSample('default'));
    }
}
