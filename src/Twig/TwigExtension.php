<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig;

/**
 * Provides Twig template discovery and rendering.
 */
class TwigExtension extends AbstractTwigExtension
{
    private $globs = [];
    private $twigRoot;
    private $twigOptions = [];

    public function __construct(array $config = [])
    {
        $this->iterator = $config['finder'] ?: new \ArrayIterator([]);
        $this->twigOptions = $config['twig_options'] ?: [];
        $this->twigRoot = $config['twig_root'] ?: getcwd();

        if (!$this->iterator instanceof \Traversable) {
            throw new \InvalidArgumentException(
                sprintf('Invalid finder passed to TwigExtension.  Finder must be an instance of \Traversable')
            );
        }

        if (!is_dir($this->twigRoot)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid twig root %s', $this->twigRoot)
            );
        }
    }

    protected function getNamespaces(): array
    {
        $namespaces = [];
        $loader = $this->getLoader();
        if ($loader instanceof \Twig_Loader_Filesystem) {
            foreach ($loader->getNamespaces() as $namespace) {
                $namespaces[$namespace] = $loader->getPaths($namespace);
            }
        }

        return $namespaces;
    }

    protected function getIterator()
    {
        return $this->iterator;
    }

    protected function getTwig(): \Twig_Environment
    {
        return new \Twig_Environment($this->getLoader(), $this->twigOptions);
    }

    protected function getTwigRoot(): string
    {
        return $this->twigRoot;
    }

    protected function getGlobs(): array
    {
        return $this->globs;
    }

    protected function getLoader(): \Twig_LoaderInterface
    {
        $root = $this->twigRoot;

        return new \Twig_Loader_Filesystem([$root], $root);
    }
}
