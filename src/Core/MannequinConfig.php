<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Config\ConfigInterface;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Ui\CheckingUiDecorator;
use LastCall\Mannequin\Core\Ui\LocalUi;
use LastCall\Mannequin\Core\Ui\UiInterface;

class MannequinConfig implements ConfigInterface
{
    private $assets;
    private $css = [];
    private $js = [];
    private $extensions = [];
    private $ui;
    private $cachePrefix = '';
    private $docroot = '';

    public function __construct(array $values = [])
    {
        $this->assets = new \ArrayIterator([]);

        if (isset($values['ui'])) {
            $this->ui = $values['ui'];
        } else {
            // Default UI.
            $composer = json_decode(file_get_contents(__DIR__.'/composer.json'));
            $composerUi = $composer->extra->{'extra-files'}->ui;
            $uiPath = sprintf('%s/%s', __DIR__, $composerUi->path);
            $this->ui = new CheckingUiDecorator(
                new LocalUi($uiPath),
                $composerUi->url,
                $uiPath
            );
        }

        $this->addExtension(new CoreExtension());
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param \LastCall\Mannequin\Core\Extension\ExtensionInterface $extension
     *
     * @return static
     */
    public function addExtension(ExtensionInterface $extension): ConfigInterface
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return static
     */
    public static function create(array $values = []): MannequinConfig
    {
        return new static($values);
    }

    public function getUi(): UiInterface
    {
        return $this->ui;
    }

    public function getGlobalCss(): array
    {
        return $this->css;
    }

    /**
     * Set the CSS files to include for every component.
     *
     * @param array $css an array of javascript URLs or paths
     *
     * @return static
     */
    public function setGlobalCss(array $css): MannequinConfig
    {
        $this->css = $css;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalJs(): array
    {
        return $this->js;
    }

    /**
     * Set the JS files to include for every component.
     *
     * @param array $js an array of javascript URLs or paths
     *
     * @return static
     */
    public function setGlobalJs(array $js): MannequinConfig
    {
        $this->js = $js;

        return $this;
    }

    /**
     * @param array|\Traversable $assets
     *
     * @return \LastCall\Mannequin\Core\MannequinConfig
     */
    public function setAssets($assets): MannequinConfig
    {
        if (is_array($assets)) {
            $assets = new \ArrayIterator($assets);
        }
        if (!$assets instanceof \Traversable) {
            throw new \InvalidArgumentException('Assets must be an iterable array or object.');
        }
        $this->assets = $assets;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets(): \Traversable
    {
        return $this->assets;
    }

    public function getDocroot(): string
    {
        return $this->docroot;
    }

    public function setDocroot(string $docroot): ConfigInterface
    {
        $this->docroot = $docroot;

        return $this;
    }

    public function getCachePrefix(): string
    {
        return $this->cachePrefix;
    }

    public function setCachePrefix(string $prefix): ConfigInterface
    {
        $this->cachePrefix = $prefix;

        return $this;
    }
}
