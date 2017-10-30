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
    private $driver;

    public function __construct(array $config = [])
    {
        $this->iterator = $config['finder'] ?: new \ArrayIterator([]);
        $this->drupalRoot = $config['drupal_root'] ?? getcwd();
        $this->twigOptions = $config['twig_options'] ?? [];
        $this->twigNamespaces = $config['twig_namespaces'] ?? [];
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

            if (!isset($this->twigOptions['cache'])) {
                $this->twigOptions['cache'] = $this->mannequin->getCacheDir();
            }
            $this->driver = new DrupalTwigDriver(
                $this->drupalRoot,
                $discovery,
                $this->twigOptions,
                $this->twigNamespaces
            );
        }

        return $this->driver;
    }
}
