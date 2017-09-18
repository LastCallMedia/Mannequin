<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Ui;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Tests\Stubs\TestFileComponent;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ManifestBuilderTest extends TestCase
{
    private function getGenerator()
    {
        $generator = $this->prophesize(UrlGeneratorInterface::class);
        $generator
            ->generate(Argument::type('string'), Argument::any(), UrlGeneratorInterface::RELATIVE_PATH)->will(function ($args) {
                $argString = '';
                foreach ($args[1] as $k => $v) {
                    $argString .= sprintf('/%s:%s', $k, $v);
                }

                return sprintf('/%s%s', $args[0], $argString);
            });

        return $generator->reveal();
    }

    public function testGeneratesManifest()
    {
        $component = new TestFileComponent('p1', ['p1-alias'], new File(__FILE__));
        $component->setName('Component 1');
        $component->addMetadata('foo', 'bar');
        $component->createSample('foo', 'Foo', new VariableSet(), ['foo' => 'bar']);
        $component->addUsedComponent($component);
        $component->addProblem('foo problem');

        $collection = new ComponentCollection([$component]);
        $builder = new ManifestBuilder($this->getGenerator());
        $manifest = $builder->generate($collection);
        $this->assertInternalType('array', $manifest);

        return $manifest;
    }

    /**
     * @depends testGeneratesManifest
     */
    public function testManifestComponent($manifest)
    {
        $this->assertTrue(isset($manifest['components']));
        $this->assertCount(1, $manifest['components']);

        return reset($manifest['components']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testSetsNameOnComponent($componentManifest)
    {
        $this->assertEquals('Component 1', $componentManifest['name']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testSetsIdOnComponent($componentManifest)
    {
        $this->assertEquals('p1', $componentManifest['id']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testSetsAliasesOnComponent($componentManifest)
    {
        $this->assertEquals(['p1-alias'], $componentManifest['aliases']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testSetsMetadataOnComponent($componentManifest)
    {
        // Avoid checking equality, since the component may have other metadata
        // we don't care about.
        $this->assertArraySubset([
            'foo' => 'bar',
        ], $componentManifest['metadata']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testSetsSourceOnComponent($componentManifest)
    {
        $this->assertEquals('/component_render_source_raw/component:p1', $componentManifest['source']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testSetsProblemsOnComponent($componentManifest)
    {
        $this->assertEquals(['foo problem'], $componentManifest['problems']);
    }

    /**
     * @depends testManifestComponent
     */
    public function testComponentSamples($componentManifest)
    {
        $this->assertInternalType('array', $componentManifest['samples']);
        $this->assertCount(1, $componentManifest['samples']);

        return reset($componentManifest['samples']);
    }

    /**
     * @depends testComponentSamples
     */
    public function testSetsIdOnSample($sampleManifest)
    {
        $this->assertEquals('foo', $sampleManifest['id']);
    }

    /**
     * @depends testComponentSamples
     */
    public function testSetsNameOnSample($sampleManifest)
    {
        $this->assertEquals('Foo', $sampleManifest['name']);
    }

    /**
     * @depends testComponentSamples
     */
    public function testSetsSourceOnSample($sampleManifest)
    {
        $this->assertEquals('/component_render_raw/component:p1/sample:foo', $sampleManifest['source']);
    }

    /**
     * @depends testComponentSamples
     */
    public function testSetsRenderedOnSample($sampleManifest)
    {
        $this->assertEquals('/component_render/component:p1/sample:foo', $sampleManifest['rendered']);
    }

    /**
     * @depends testComponentSamples
     */
    public function testSetsMetadataOnSample($sampleManifest)
    {
        $this->assertArraySubset([
            'foo' => 'bar',
        ], $sampleManifest['metadata']);
    }
}
