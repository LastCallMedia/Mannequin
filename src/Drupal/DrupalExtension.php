<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal;

use Drupal\Core\Template\Attribute;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Drupal\Driver\DrupalTwigDriver;
use LastCall\Mannequin\Drupal\Drupal\MannequinExtensionDiscovery;
use LastCall\Mannequin\Twig\AbstractTwigExtension;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Provides Drupal Twig template discovery and rendering.
 */
class DrupalExtension extends AbstractTwigExtension implements ExpressionFunctionProviderInterface
{
    private $iterator;
    private $drupalRoot;
    private $twigOptions;
    private $twigNamespaces = [];
    private $fallbackExtensions = ['stable'];
    private $driver;

    public function __construct(array $config = [])
    {
        $this->iterator = $config['finder'] ?: new \ArrayIterator([]);
        $this->drupalRoot = $config['drupal_root'] ?? getcwd();
        $this->twigOptions = $config['twig_options'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $attributes = new ExpressionFunction('attributes', function ($args) {
            throw new \InvalidArgumentException('Attributes cannot be compiled.');
        }, function ($args, $attrs) {
            return new Attribute($attrs);
        });

        return [$attributes];
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
        $this->twigNamespaces[$namespace][] = $path;

        return $this;
    }

    /**
     * Add a Drupal extension to use as a "fallback" when a twig name can't be resolved.
     *
     * This exists to emulate the Drupal theme registry loader, which loads
     * unqualified import/extend statements from the registry.  Since we don't
     * have a registry, we support loading those unqualified imports from
     * specified themes/modules.  This allows you to emulate the template
     * inheritance chain.
     *
     * @param string[] $moduleOrThemeName an array of module or theme names
     *
     * @return \LastCall\Mannequin\Core\Extension\ExtensionInterface
     */
    public function setFallbackExtensions(array $moduleOrThemeNames): ExtensionInterface
    {
        $this->fallbackExtensions = $moduleOrThemeNames;

        return $this;
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
            $discovery = new MannequinExtensionDiscovery($this->drupalRoot, $this->mannequin->getCache());
            $this->driver = new DrupalTwigDriver(
                $this->drupalRoot,
                $discovery,
                $this->twigOptions,
                $this->twigNamespaces,
                $this->fallbackExtensions
            );
            $this->driver->setCache(new \Twig_Cache_Filesystem($this->mannequin->getCacheDir().'/twig'));
        }

        return $this->driver;
    }
}
