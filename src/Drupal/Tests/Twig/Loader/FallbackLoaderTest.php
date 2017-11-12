<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests\Twig\Loader;

use LastCall\Mannequin\Drupal\Twig\Loader\FallbackLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FallbackLoaderTest extends TestCase
{
    public function getRoot()
    {
        $stream = vfsStream::setup('root', null, [
            'p1' => [
                't1' => 't1.p1',
                'd1' => [
                    't2' => 't2.p1',
                    'd2' => [
                        't3' => 't3.p1',
                    ],
                ],
            ],
            'p2' => [
                't1.p2',
                'd1' => [
                    't2' => 't2.p2',
                    'd2' => [
                        't3' => 't3.p2',
                        't4' => 't4.p2',
                    ],
                ],
            ],
        ]);

        return $stream;
    }

    public function getExistsTests()
    {
        return [
            ['t1', true, 'Existing templates at the root should be checked'],
            ['t2', true, 'Existing templates in nested directories should be checked'],
            ['t3', true, 'Existing templates in deeply nested directories should be checked'],
            ['d1/t2', false, 'Template paths that include a directory separator should not be checked'],
        ];
    }

    /**
     * @dataProvider getExistsTests
     */
    public function testExists($input, $expected, $message)
    {
        $stream = $this->getRoot();
        $loader = new FallbackLoader([
            $stream->getChild('p1')->url(),
            $stream->getChild('p2')->url(),
        ]);
        $this->assertEquals($expected, $loader->exists($input), $message);
    }

    public function testWithCache()
    {
        $stream = $this->getRoot();
        $loader = new FallbackLoader([
            $stream->getChild('p1')->url(),
            $stream->getChild('p2')->url(),
        ]);
        $sc = $loader->getSourceContext('t1');
        $this->assertEquals($sc, $loader->getSourceContext('t1'));
    }

    public function getSourceTests()
    {
        return [
            ['t1', 't1.p1', 'Template should be resolved from the first path that matches'],
            ['t4', 't4.p2', 'Should check all paths before returning false.'],
        ];
    }

    /**
     * @dataProvider getSourceTests
     */
    public function testSource($input, $expected, $message)
    {
        $stream = $this->getRoot();
        $loader = new FallbackLoader([
            $stream->getChild('p1')->url(),
            $stream->getChild('p2')->url(),
        ]);
        $context = $loader->getSourceContext($input);
        $this->assertEquals($expected, $context->getCode(), $message);
    }
}
