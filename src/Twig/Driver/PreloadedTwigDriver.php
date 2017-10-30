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

/**
 * Uses a Twig_Environment that is already created.
 *
 * This driver is mostly used for testing.  It has no way to link the namespaces
 * with the actual Twig loader.
 */
class PreloadedTwigDriver extends AbstractTwigDriver
{
    private $twigWrapped;
    private $twigRoot;
    private $namespaces = [];

    public function __construct(\Twig_Environment $twig, string $twigRoot = '', array $namespaces = [])
    {
        $this->twigWrapped = function () use ($twig) {
            return $twig;
        };
        $this->twigRoot = $twigRoot;
        $this->namespaces = $namespaces;
    }

    protected function createTwig(): \Twig_Environment
    {
        $fn = $this->twigWrapped;

        return $fn();
    }

    protected function getTwigRoot(): string
    {
        return $this->twigRoot;
    }

    protected function getNamespaces(): array
    {
        return $this->namespaces;
    }
}
