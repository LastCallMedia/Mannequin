<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Stubs;

use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;

class TestFilePattern extends AbstractPattern implements TemplateFilePatternInterface
{
    private $file;

    public function __construct($id, array $aliases = [], \SplFileInfo $file)
    {
        parent::__construct($id, $aliases);
        $this->file = $file;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getRawFormat(): string
    {
        return 'html';
    }
}
