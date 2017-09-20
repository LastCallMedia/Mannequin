<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Driver;

class PreloadedTwigDriver implements TwigDriverInterface
{
    private $twigRoot;

    public function __construct(\Twig_Environment $twig, string $twigRoot = '', array $namespaces = [])
    {
        $this->twig = $twig;
        $this->namespaces = $namespaces;
        $this->twigRoot = $twigRoot;
    }

    public function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    public function getTwigRoot(): string
    {
        return $this->twigRoot;
    }
}
