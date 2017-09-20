<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Component;

/**
 * This component class can be used by discoverers when a component is so broken
 * that it is not loadable.
 */
class BrokenComponent extends AbstractComponent implements TemplateFileInterface
{
    private $file;

    /**
     * @param \SplFileInfo|null $file
     */
    public function setFile(\SplFileInfo $file = null)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file ?? false;
    }
}
