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

use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\RemoteUi;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Pimple\Container;

class MannequinConfig extends Container implements ConfigInterface
{
    public function __construct(array $values = [])
    {
        $values += [
            'ui' => function () {
                $composer = json_decode(file_get_contents(__DIR__.'/composer.json'), true);

                return new RemoteUi(sys_get_temp_dir().'/mannequin-ui', $composer['extra']['uiVersion']);
            },
            'global_css' => [],
            'global_js' => [],
            'assets' => [],
        ];
        parent::__construct($values);
        $this['extensions'] = function () {
            return [new CoreExtension()];
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(): array
    {
        return $this['extensions'];
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extension): ConfigInterface
    {
        $this->extend(
            'extensions',
            function (array $extensions) use ($extension) {
                $extensions[] = $extension;

                return $extensions;
            }
        );

        return $this;
    }

    public static function create(array $values = []): MannequinConfig
    {
        return new static($values);
    }

    /**
     * @return PatternCollection
     */
    public function getCollection(): PatternCollection
    {
        return $this['collection'];
    }

    public function getUi(): UiInterface
    {
        return $this['ui'];
    }

    public function getGlobalCss(): array
    {
        return $this['global_css'];
    }

    public function setGlobalCss(array $styles): MannequinConfig
    {
        $this['global_css'] = $styles;

        return $this;
    }

    public function getGlobalJs(): array
    {
        return $this['global_js'];
    }

    public function setGlobalJs(array $js): MannequinConfig
    {
        $this['global_js'] = $js;

        return $this;
    }

    public function setAssets($assets): MannequinConfig
    {
        if (is_array($assets) || !$assets instanceof \Traversable) {
            throw new \InvalidArgumentException('Assets must be an iterable array or object.');
        }
        $this['assets'] = $assets;

        return $this;
    }

    public function getAssets(): \Traversable
    {
        if (is_array($this['assets'])) {
            return new \ArrayIterator($this['assets']);
        }

        return $this['assets'];
    }
}
