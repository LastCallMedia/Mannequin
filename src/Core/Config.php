<?php

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Engine\DelegatingEngine;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Core\Variable\VariableFactory;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Config extends Container implements ConfigInterface
{

    public function __construct(array $values = [])
    {
        $values += [
            'cache_dir' => __DIR__.'/../cache',
            'styles' => [],
            'scripts' => [],
        ];
        parent::__construct($values);
        $this['labeller'] = function () {
            return new Labeller();
        };
        $this['extensions'] = function () {
            return [];
        };
        $this['discovery'] = function () {
            $discoverers = [];
            foreach ($this->getExtensions() as $extension) {
                $discoverers = array_merge(
                    $discoverers,
                    $extension->getDiscoverers()
                );
            }

            return new ChainDiscovery($discoverers, $this->getDispatcher());
        };
        $this['renderer'] = function () {
            $renderers = [];
            foreach ($this->getExtensions() as $extension) {
                $renderers = array_merge(
                    $renderers,
                    $extension->getRenderers()
                );
            }

            return new DelegatingEngine($renderers);
        };
        $this['variable.resolver'] = function () {
            $resolvers = [];
            foreach ($this->getExtensions() as $extension) {
                $resolvers = array_merge(
                    $resolvers,
                    $extension->getVariableResolvers()
                );
            }

            return new SetResolver($resolvers);
        };
        $this['collection'] = function () {
            return $this['discovery']->discover();
        };
        $this['assets'] = function () {
            return [];
        };
        $this['dispatcher'] = function () {
            $dispatcher = new EventDispatcher();
            foreach ($this->getExtensions() as $extension) {
                $extension->attachToDispatcher($dispatcher);
            }

            return $dispatcher;
        };

        $this->addExtension(new CoreExtension());
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(): array
    {
        return $this['extensions'];
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this['dispatcher'];
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extension): ConfigInterface
    {
        $this->extend(
            'extensions',
            function (array $extensions) use ($extension) {
                $extension->setConfig($this);
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

    /**
     * @return \LastCall\Mannequin\Core\Engine\EngineInterface
     */
    public function getRenderer(): EngineInterface
    {
        return $this['renderer'];
    }

    /**
     * {@inheritdoc}
     */
    public function getLabeller(): Labeller
    {
        return $this['labeller'];
    }

    public function getVariableResolver(): SetResolver
    {
        return $this['variable.resolver'];
    }

    public function getCacheDir(): string
    {
        return $this['cache_dir'];
    }
}