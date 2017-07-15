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

use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use PHPUnit\Framework\TestCase;

class MappingCallbackIteratorTest extends TestCase
{
    public function testInvokesCallback()
    {
        $iterator = new \ArrayIterator([__FILE__]);
        $callback = function ($filename) {
            return $filename.'.foo';
        };
        $mapper = new MappingCallbackIterator($iterator, $callback);
        $this->assertEquals(
            [
                __FILE__.'.foo',
            ],
            iterator_to_array($mapper)
        );
    }

    public function testPassesThroughSplFileInfo()
    {
        $fileInfo = new \SplFileInfo(__FILE__);
        $iterator = new \ArrayIterator([$fileInfo]);
        $callback = function ($passedFileInfo) use ($fileInfo) {
            $this->assertSame($fileInfo, $passedFileInfo);

            return 'foo';
        };
        $mapper = new MappingCallbackIterator($iterator, $callback);
        $this->assertEquals(
            [
                'foo',
            ],
            iterator_to_array($mapper)
        );
    }
}
