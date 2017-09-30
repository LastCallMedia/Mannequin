<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Iterator;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Invokable class to convert absolute paths to relative SplFileInfo objects.
 */
class RelativePathMapper
{
    private $root;

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    public function __invoke($filename)
    {
        if (strpos($filename, $this->root) !== 0) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to determine relative path for %s.  It is outside of %s.',
                $filename,
                $this->root
            ));
        }

        $relativePathName = ltrim(substr($filename, strlen($this->root)), '/\\');
        if ('.' === $relativePath = dirname($relativePathName)) {
            $relativePath = '';
        } else {
            $relativePath .= DIRECTORY_SEPARATOR;
        }

        return new SplFileInfo(
            $filename,
            $relativePath,
            $relativePathName
        );
    }
}
