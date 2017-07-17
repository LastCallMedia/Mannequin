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
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Variable\Set;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ManifesterTest extends TestCase
{
    public function getGenerateTests()
    {
        $s1 = $this->prophesize(Set::class);
        $s1->getName()->willReturn('foo');
        $s1->getDescription()->willReturn('s1 description');

        $p1 = $this->prophesize(PatternInterface::class);
        $p1->getId()->willReturn('p1');
        $p1->getAliases()->willReturn(['p1-alias']);
        $p1->getName()->willReturn('Pattern 1');
        $p1->getDescription()->willReturn('This is pattern 1');
        $p1->getTags()->willReturn(['foo' => 'bar']);
        $p1->getVariableSets()->willReturn(['default' => $s1]);
        $p1->getUsedPatterns()->willReturn([$p1]);
        $p1->getRawFormat()->willReturn('html');

        $collection = new PatternCollection([$p1->reveal()]);
        $expected = [
            'patterns' => [
                [
                    'id' => 'p1',
                    'name' => 'Pattern 1',
                    'description' => 'This is pattern 1',
                    'tags' => ['foo' => 'bar'],
                    'aliases' => ['p1-alias'],
                    'used' => ['p1'],
                    'source' => '/pattern_render_source_raw/pattern:p1',
                    'format' => 'html',
                    'sets' => [
                        [
                            'id' => 'default',
                            'name' => 'foo',
                            'description' => 's1 description',
                            'source' => '/pattern_render_raw/pattern:p1/set:default',
                            'rendered' => '/pattern_render/pattern:p1/set:default',
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
