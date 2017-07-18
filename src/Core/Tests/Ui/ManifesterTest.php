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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ManifesterTest extends TestCase
{
    public function getGenerateTests()
    {
        $pattern = new TestFilePattern('p1', ['p1-alias'], new File(__FILE__));
        $pattern->setName('Pattern 1');
        $pattern->addTag('foo', 'bar');
        $pattern->createVariant('foo', 'Foo', [], ['foo' => 'bar']);
        $pattern->addUsedPattern($pattern);

        $collection = new PatternCollection([$pattern]);
        $expected = [
            'patterns' => [
                [
                    'id' => 'p1',
                    'name' => 'Pattern 1',
                    'tags' => [
                        'foo' => 'bar',
                        'category' => 'Unknown',
                        'source_format' => 'html',
                    ],
                    'aliases' => ['p1-alias'],
                    'used' => ['p1'],
                    'source' => '/pattern_render_source_raw/pattern:p1',
                    'variants' => [
                        [
                            'id' => 'foo',
                            'name' => 'Foo',
                            'source' => '/pattern_render_raw/pattern:p1/variant:foo',
                            'rendered' => '/pattern_render/pattern:p1/variant:foo',
                            'tags' => ['foo' => 'bar'],
                        ],
                    ],
                ],
            ],
        ];

        return [
            [$collection, $expected],
        ];
    }

    /**
     * @dataProvider getGenerateTests
     */
    public function testGenerate(PatternCollection $collection, $expected)
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
        $builder = new ManifestBuilder($generator->reveal());
        $actual = $builder->generate($collection);
        $this->assertEquals($expected, $actual);
    }
}
