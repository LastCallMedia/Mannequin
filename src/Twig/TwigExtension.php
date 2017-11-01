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

use LastCall\Mannequin\Twig\Driver\SimpleTwigDriver;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;

/**
 * Provides Twig template discovery and rendering.
 */
class TwigExtension extends AbstractTwigExtension
{
    private $iterator;
    private $twigRoot;
    private $twigOptions;
    private $twigNamespaces = [];
    protected $driver;

    public function __construct(array $config = [])
    {
        $this->iterator = $config['finder'] ?: new \ArrayIterator([]);

        if (!$this->iterator instanceof \Traversable) {
            throw new \InvalidArgumentException(
                sprintf('Invalid finder passed to TwigExtension.  Finder must be an instance of \Traversable')
            );
        }
        $this->twigRoot = $config['twig_root'] ?? getcwd();
        $this->twigOptions = $config['twig_options'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateFilenameIterator(): \Traversable
    {
        return $this->iterator;
    }

    /**
     * Add a directory to the Twig loader.
     *
     * @param string $namespace the twig namespace the path should be added to
     * @param string $path      the template directory to add
     *
     * @return $this
     */
    public function addTwigPath(string $namespace, string $path): ExtensionInterface
    {
        if ($this->driver) {
            throw new \RuntimeException('Driver has already been created.  Namespaces cannot be added.');
        }
        $this->twigNamespaces[$namespace][] = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDriver(): TwigDriverInterface
    {
        if (!$this->driver) {
            $this->driver = new SimpleTwigDriver(
                $this->twigRoot,
                $this->twigOptions,
                $this->twigNamespaces
            );
            $this->driver->setCache(new \Twig_Cache_Filesystem($this->mannequin->getCacheDir().'/twig'));
        }

        return $this->driver;
    }
}
