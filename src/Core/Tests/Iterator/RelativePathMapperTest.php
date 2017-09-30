<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Iterator;

use LastCall\Mannequin\Core\Iterator\RelativePathMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class RelativePathMapperTest extends TestCase
{
    public function getMapTests()
    {
        return [
            [
                new SplFileInfo('/foo/bar/baz', 'bar/', 'bar/baz'),
                '/foo',
                '/foo/bar/baz',
            ],
            [
                new SplFileInfo('/foo/bar/baz', 'foo/bar/', 'foo/bar/baz'),
                '/',
                '/foo/bar/baz',
            ],
            [
                new SplFileInfo('/foo/bar/baz', '', 'baz'),
                '/foo/bar',
                '/foo/bar/baz',
            ],
            [
                new SplFileInfo('/😁/😂/😍', '😂/', '😂/😍'),
                '/😁',
                '/😁/😂/😍',
            ],
        ];
    }

    /**
     * @dataProvider getMapTests()
     */
    public function testMap($expected, $root, $input)
    {
        $mapper = new RelativePathMapper($root);
        $this->assertEquals($expected, $mapper($input));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to determine relative path for /bar.  It is outside of /foo.
     */
    public function testThrowsExceptionForFilesOutsideRoot()
    {
        (new RelativePathMapper('/foo'))('/bar');
    }
}
