<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Pattern;

use LastCall\Mannequin\Core\Pattern\AbstractPattern;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;

class TwigPattern extends AbstractPattern implements TemplateFilePatternInterface
{
    private $source;

    public function __construct($id, array $aliases = [], \Twig_Source $source)
    {
        parent::__construct($id, $aliases);
        $this->aliases = $aliases;
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getFile(): \SplFileInfo
    {
        return new \SplFileInfo($this->source->getPath());
    }

    protected static function getDefaultTags(): array
    {
        return [
            'source_format' => 'twig',
        ] + parent::getDefaultTags();
    }
}
