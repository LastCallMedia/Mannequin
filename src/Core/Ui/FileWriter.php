<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui;

use Symfony\Component\Filesystem\Filesystem;

class FileWriter
{
    private $dir;

    private $fs;

    public function __construct($dir)
    {
        $this->dir = $dir;
        $this->fs = new Filesystem();
    }

    public function raw($path, $contents)
    {
        $this->fs->dumpFile(sprintf('%s/%s', $this->dir, $path), $contents);
    }

    public function copy($src, $dest)
    {
        if (is_file($src)) {
            $this->fs->copy($src, sprintf('%s/%s', $this->dir, $dest));

            return;
        } elseif (is_dir($src)) {
            $this->fs->mirror($src, sprintf('%s/%s', $this->dir, $dest));

            return;
        }
        throw new \RuntimeException(
            sprintf('Source file does not exist: %s', $src)
        );
    }
}
