<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Pattern;

use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;

class HtmlPattern extends AbstractPattern implements TemplateFilePatternInterface
{
    private $fileInfo;

    public function __construct($id, array $aliases, \SplFileInfo $fileInfo)
    {
        parent::__construct($id, $aliases);
        $this->fileInfo = $fileInfo;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->fileInfo;
    }

    public function getRawFormat(): string
    {
        return 'html';
    }
}
