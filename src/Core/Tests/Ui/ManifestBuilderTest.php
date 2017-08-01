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

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Tests\Stubs\TestFilePattern;
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
        $pattern = new TestFilePattern('p1', ['p1-alias'], new File(__FILE__));
        $pattern->setName('Pattern 1');
        $pattern->addTag('foo', 'bar');
        $pattern->createVariant('foo', 'Foo', new VariableSet(), ['foo' => 'bar']);
        $pattern->addUsedPattern($pattern);
        $pattern->addProblem('foo problem');

        $collection = new PatternCollection([$pattern]);
        $builder = new ManifestBuilder($this->getGenerator());
        $manifest = $builder->generate($collection);
        $this->assertInternalType('array', $manifest);

        return $manifest;
    }

    /**
     * @depends testGeneratesManifest
     */
    public function testManifestPattern($manifest)
    {
        $this->assertTrue(isset($manifest['patterns']));
        $this->assertCount(1, $manifest['patterns']);

        return reset($manifest['patterns']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testSetsNameOnPattern($patternManifest)
    {
        $this->assertEquals('Pattern 1', $patternManifest['name']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testSetsIdOnPattern($patternManifest)
    {
        $this->assertEquals('p1', $patternManifest['id']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testSetsAliasesOnPattern($patternManifest)
    {
        $this->assertEquals('Pattern 1', $patternManifest['name']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testSetsTagsOnPattern($patternManifest)
    {
        // Avoid checking equality, since the pattern may have other tags
        // we don't care about.
        $this->assertArraySubset([
            'foo' => 'bar',
        ], $patternManifest['tags']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testSetsSourceOnPattern($patternManifest)
    {
        $this->assertEquals('/pattern_render_source_raw/pattern:p1', $patternManifest['source']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testSetsProblemsOnPattern($patternManifest)
    {
        $this->assertEquals(['foo problem'], $patternManifest['problems']);
    }

    /**
     * @depends testManifestPattern
     */
    public function testPatternVariants($patternManifest)
    {
        $this->assertInternalType('array', $patternManifest['variants']);
        $this->assertCount(1, $patternManifest['variants']);

        return reset($patternManifest['variants']);
    }

    /**
     * @depends testPatternVariants
     */
    public function testSetsIdOnVariant($variantManifest)
    {
        $this->assertEquals('foo', $variantManifest['id']);
    }

    /**
     * @depends testPatternVariants
     */
    public function testSetsNameOnVariant($variantManifest)
    {
        $this->assertEquals('Foo', $variantManifest['name']);
    }

    /**
     * @depends testPatternVariants
     */
    public function testSetsSourceOnVariant($variantManifest)
    {
        $this->assertEquals('/pattern_render_raw/pattern:p1/variant:foo', $variantManifest['source']);
    }

    /**
     * @depends testPatternVariants
     */
    public function testSetsRenderedOnVariant($variantManifest)
    {
        $this->assertEquals('/pattern_render/pattern:p1/variant:foo', $variantManifest['rendered']);
    }

    /**
     * @depends testPatternVariants
     */
    public function testSetsTagsOnVariant($variantManifest)
    {
        $this->assertArraySubset([
            'foo' => 'bar',
        ], $variantManifest['tags']);
    }
}
