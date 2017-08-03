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

use LastCall\Mannequin\Core\Cache\NullCacheItemPool;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\RemoteUi;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Pimple\Container;
use Psr\Cache\CacheItemPoolInterface;

class MannequinConfig extends Container implements ConfigInterface
{
    public function __construct(array $values = [])
    {
        $values += [
            'cache' => function () {
                return new NullCacheItemPool();
            },
            'ui' => function () {
                $composer = json_decode(file_get_contents(__DIR__.'/composer.json'), true);

                return new RemoteUi(sys_get_temp_dir().'/mannequin-ui', $composer['extra']['uiVersion']);
            },
            'styles' => [],
            'scripts' => [],
        ];
        parent::__construct($values);
        $this['extensions'] = function () {
            return [new CoreExtension()];
        };
        $this['assets'] = function () {
            return [];
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

    public static function create(array $values = []): ConfigInterface
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

    /**
     * {@inheritdoc}
     */
    public function getStyles(): array
    {
        return $this['styles'];
    }

    /**
     * {@inheritdoc}
     */
    public function getScripts(): array
    {
        return $this['scripts'];
    }

    public function addAssetMapping($url, $path): ConfigInterface
    {
        if (!is_string($url) || strlen($url) === 0 || strpos($url, '/') === 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    'URL path specified for %s is invalid.  It should be a relative URL.',
                    $path
                )
            );
        }
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(
                sprintf('Path specified for asset url %s is invalid.', $url)
            );
        }
        $this->extend(
            'assets',
            function (array $existing) use ($url, $path) {
                $existing[$url] = $path;

                return $existing;
            }
        );

        return $this;
    }

    public function getAssetMappings(): array
    {
        return $this['assets'];
    }

    public function getCache(): CacheItemPoolInterface
    {
        return $this['cache'];
    }

    public function getUi(): UiInterface
    {
        return $this['ui'];
    }
}
