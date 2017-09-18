<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Component;

use LastCall\Mannequin\Core\Component\AbstractComponent;
use LastCall\Mannequin\Core\Component\TemplateFileInterface;

class HtmlComponent extends AbstractComponent implements TemplateFileInterface
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
}
