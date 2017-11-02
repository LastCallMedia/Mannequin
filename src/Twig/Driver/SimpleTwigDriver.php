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
 * Knows how to create a simple Twig_Environment with a known root and options.
 *
 * This driver depends on namespaces being determined in advance.  It passes
 * those namespaces as paths to the \Twig_Loader_Fileystem.
 */
class SimpleTwigDriver extends AbstractTwigDriver
{
    private $twigRoot;
    private $twigOptions;
    private $namespaces = [];

    public function __construct(string $twigRoot, array $twigOptions = [], array $namespaces = [])
    {
        if (!is_dir($twigRoot)) {
            throw new \InvalidArgumentException(sprintf('Invalid Twig root given: ', $twigRoot));
        }
        $this->twigRoot = $twigRoot;
        $this->twigOptions = $twigOptions;
        foreach ($namespaces as $namespace => $paths) {
            if (!is_array($paths)) {
                throw new \InvalidArgumentException(sprintf('Namespace paths must be an array under the %s namespace', $namespace));
            }
            $this->namespaces[$namespace] = $paths;
        }
    }

    protected function createTwig(): \Twig_Environment
    {
        return new \Twig_Environment(
            $this->createLoader(),
            $this->twigOptions
        );
    }

    protected function createLoader()
    {
        $loader = new \Twig_Loader_Filesystem([''], $this->twigRoot);
        foreach ($this->getNamespaces() as $namespace => $paths) {
            foreach ($paths as $path) {
                $loader->addPath($path, $namespace);
            }
        }

        return $loader;
    }

    protected function getNamespaces(): array
    {
        return $this->namespaces;
    }

    protected function getTwigRoot(): string
    {
        return $this->twigRoot;
    }
}
