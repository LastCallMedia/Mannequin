<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Config;

use LastCall\Mannequin\Core\Ui\UiInterface;

/**
 * This decorator class wraps a ConfigInterface object to provide the methods
 * we need to reload a configuration object from scratch.
 */
class ReaddressableConfigDecorator implements ReaddressableConfigInterface
{
    private $inner;
    private $sourceFile;
    private $autoloadPath;

    public function __construct(ConfigInterface $inner, string $sourceFile, string $autoloadFile)
    {
        $this->inner = $inner;
        $this->sourceFile = $sourceFile;
        $this->autoloadPath = $autoloadFile;
    }

    public function getSourceFile(): string
    {
        return $this->sourceFile;
    }

    public function getAutoloadFile(): string
    {
        return $this->autoloadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(): array
    {
        return $this->inner->getExtensions();
    }

    public function getUi(): UiInterface
    {
        return $this->inner->getUi();
    }

    public function getGlobalCss(): array
    {
        return $this->inner->getGlobalCss();
    }

    public function getGlobalJs(): array
    {
        return $this->inner->getGlobalJs();
    }

    public function getAssets(): \Traversable
    {
        return $this->inner->getAssets();
    }

    public function getCid(): string
    {
        $innerCid = $this->inner->getCid();
        if ('' === $innerCid) {
            return md5($this->sourceFile);
        }

        return $innerCid;
    }

    public function getDocroot(): string
    {
        $innerDocroot = $this->inner->getDocroot();

        if ('' === $innerDocroot) {
            return dirname($this->sourceFile);
        }

        return $innerDocroot;
    }
}
