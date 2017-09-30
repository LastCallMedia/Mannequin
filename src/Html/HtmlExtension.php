<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html;

use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use LastCall\Mannequin\Core\Iterator\RelativePathMapper;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Engine\HtmlEngine;

/**
 * Provides HTML file discovery and rendering.
 */
class HtmlExtension extends AbstractExtension
{
    private $files;
    private $root;

    public function __construct(array $values = [])
    {
        $files = $values['files'] ?? [];
        if (is_array($files)) {
            $files = new \ArrayIterator($files);
        }
        $this->files = $files;
        if (!$this->files instanceof \Traversable) {
            throw new \InvalidArgumentException("HtmlExtension 'files' option must be set to an iterable object.");
        }
        $this->root = $values['root'] ?? getcwd();
        if (!$this->root || !is_dir($this->root)) {
            throw new \InvalidArgumentException("HtmlExtension 'root' option must be set to a valid directory.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscoverers(): array
    {
        return [
            new HtmlDiscovery($this->getIterator()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEngines(): array
    {
        return [
            new HtmlEngine(),
        ];
    }

    private function getIterator()
    {
        return new MappingCallbackIterator(
            $this->files,
            new RelativePathMapper($this->root)
        );
    }
}
