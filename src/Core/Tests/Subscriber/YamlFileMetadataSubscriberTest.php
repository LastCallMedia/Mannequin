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

use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\Tests\Stubs\TestFilePattern;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class YamlFileMetadataSubscriberTest extends TestCase
{
    use DiscoverySubscriberTestTrait;
    use YamlParserProphecyTrait;

    public function setUp()
    {
        parent::setUp();
        $this->yamlFile = sys_get_temp_dir().'/test.yml';
        $this->templateFile = sys_get_temp_dir().'/test.html';
        (new Filesystem())->touch($this->yamlFile);
    }

    public function testSetsName()
    {
        $parser = $this->getParserProphecy(['name' => 'foo']);
        $pattern = new TestFilePattern(
            'foo',
            [],
            new \SplFileInfo($this->templateFile)
        );
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );
        $this->assertEquals('foo', $event->getPattern()->getName());
    }

    public function testSetsTags()
    {
        $parser = $this->getParserProphecy(['tags' => ['foo' => 'bar']]);
        $pattern = new TestFilePattern(
            'foo',
            [],
            new \SplFileInfo($this->templateFile)
        );
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );
        $this->assertArraySubset(['foo' => 'bar'], $event->getPattern()->getTags());
    }

    public function testOverridesTags()
    {
        $parser = $this->getParserProphecy(['tags' => ['category' => 'baz']]);
        $pattern = new TestFilePattern(
            'foo',
            [],
            new \SplFileInfo($this->templateFile)
        );
        $event = $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );
        $this->assertArraySubset(['category' => 'baz'], $event->getPattern()->getTags());
    }

    public function testCanSetDefaultSet()
    {
        $parser = $this->getParserProphecy(
            ['variants' => ['default' => ['name' => 'Overridden', 'values' => []]]]
        );
        $pattern = new TestFilePattern(
            'foo',
            [],
            new \SplFileInfo($this->templateFile)
        );
        $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );

        $this->assertEquals('Overridden', $pattern->getVariant('default')->getName());
    }

    public function testCanSetAdditionalSet()
    {
        $parser = $this->getParserProphecy(
            ['variants' => ['additional' => ['name' => 'Additional', 'values' => []]]]
        );
        $pattern = new TestFilePattern(
            'foo',
            [],
            new \SplFileInfo($this->templateFile)
        );
        $this->dispatchDiscover(
            new YamlFileMetadataSubscriber($parser->reveal()),
            $pattern
        );

        $this->assertTrue($pattern->hasVariant('additional'));
        $this->assertCount(1, $pattern->getVariants());
    }
}
