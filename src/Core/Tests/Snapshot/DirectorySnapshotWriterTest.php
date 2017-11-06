<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Snapshot;

use LastCall\Mannequin\Core\Snapshot\DirectorySnapshotWriter;
use LastCall\Mannequin\Core\Snapshot\Snapshot;
use LastCall\Mannequin\Core\Snapshot\SnapshotFile;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class DirectorySnapshotWriterTest extends TestCase
{
    private function getDevelopDir($subdir): vfsStreamDirectory
    {
        $stream = vfsStream::setup('root', null, [
            $subdir => [],
        ]);

        return $stream->getChild($subdir);
    }

    public function testWritesForGenerator()
    {
        $dir = $this->getDevelopDir(__FUNCTION__);
        $gen = function () {
            foreach ([1, 2] as $i) {
                yield new SnapshotFile($i, $i);
            }
        };
        $snapshot = new Snapshot($gen());
        $developer = new DirectorySnapshotWriter($dir->url());
        $developer->write($snapshot);
        foreach ([1, 2] as $i) {
            $item = $dir->getChild((string) $i)->url();
            $this->assertTrue(is_file($item));
            $this->assertEquals($i, file_get_contents($item));
        }
    }

    public function testWritesForIterator()
    {
        $dir = $this->getDevelopDir(__FUNCTION__);
        $iterator = new \ArrayIterator([
            new SnapshotFile('1', '1'),
            new SnapshotFile('2', '2'),
        ]);
        $snapshot = new Snapshot($iterator);
        $developer = new DirectorySnapshotWriter($dir->url());
        $developer->write($snapshot);
        foreach ([1, 2] as $i) {
            $item = $dir->getChild((string) $i)->url();
            $this->assertTrue(is_file($item));
            $this->assertEquals($i, file_get_contents($item));
        }
    }
}
