<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Component;

use LastCall\Mannequin\Core\Component\AbstractComponent;
use LastCall\Mannequin\Core\Component\TemplateFileInterface;

class TwigComponent extends AbstractComponent implements TemplateFileInterface
{
    private $source;
    private $twig;

    public function __construct($id, array $aliases = [], \Twig_Source $source, \Twig_Environment $twig)
    {
        parent::__construct($id, $aliases);
        $this->source = $source;
        $this->twig = $twig;
    }

    public function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }

    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        if ('' === $this->source->getPath()) {
            return false;
        }

        return new \SplFileInfo($this->source->getPath());
    }

    protected static function getDefaultMetadata(): array
    {
        return [
            'source_format' => 'twig',
        ] + parent::getDefaultMetadata();
    }
}
