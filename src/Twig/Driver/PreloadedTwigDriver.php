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

use LastCall\Mannequin\Twig\Twig\Lexer;
use LastCall\Mannequin\Twig\Twig\MannequinExtension;

/**
 * Uses a Twig_Environment that is already created.
 *
 * This driver is mostly used for testing.
 */
class PreloadedTwigDriver implements TwigDriverInterface
{
    private $twig;
    private $twigRoot;
    private $namespaces;
    private $initialized;

    public function __construct(\Twig_Environment $twig, string $twigRoot = '', array $namespaces = [])
    {
        $this->twig = $twig;
        $this->namespaces = $namespaces;
        $this->twigRoot = $twigRoot;
    }

    public function getTwig(): \Twig_Environment
    {
        if (!$this->initialized) {
            $this->initialized = true;
            $this->twig->addExtension(new MannequinExtension());
            $this->twig->setLexer(new Lexer());
        }

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
