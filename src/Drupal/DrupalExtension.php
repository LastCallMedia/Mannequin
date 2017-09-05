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
    private $driver;

    public function __construct(array $config = [])
    {
        $this->iterator = $config['finder'] ?: new \ArrayIterator([]);
        $this->drupalRoot = $config['drupal_root'] ?? getcwd();
        $this->twigOptions = $config['twig_options'] ?? [];
    }

    public function register(Mannequin $mannequin)
    {
        $this->driver->setCache($mannequin->getCache());
    }

    public function getFunctions()
    {
        $attributes = new ExpressionFunction('attributes', function ($args) {
            throw new \InvalidArgumentException('Attributes cannot be compiled.');
        }, function ($args, $attrs) {
            return new Attribute($attrs);
        });

        return [$attributes];
    }

    protected function getIterator(): \Traversable
    {
        return $this->iterator;
    }

    protected function getDriver(): TwigDriverInterface
    {
        if (!$this->driver) {
            if (!isset($this->twigOptions['cache'])) {
                $this->twigOptions['cache'] = $this->mannequin->getCacheDir();
            }
            $this->driver = new DrupalTwigDriver(
                $this->drupalRoot,
                $this->twigOptions,
                $this->mannequin->getCache()
            );
        }

        return $this->driver;
    }
}
