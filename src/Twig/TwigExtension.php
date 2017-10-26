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

/**
 * Provides Twig template discovery and rendering.
 */
class TwigExtension extends AbstractTwigExtension
{
    private $iterator;
    private $twigRoot;
    private $twigOptions;
    private $driver;

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
     * {@inheritdoc}
     */
    protected function getDriver(): TwigDriverInterface
    {
        if (!$this->driver) {
            if (!isset($this->twigOptions['cache'])) {
                $this->twigOptions['cache'] = $this->mannequin->getCacheDir().'/twig';
            }
            $this->driver = new SimpleTwigDriver(
                $this->twigRoot,
                $this->twigOptions
            );
        }

        return $this->driver;
    }
}
