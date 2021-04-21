<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Driver;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ExtensionDiscovery;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use LastCall\Mannequin\Drupal\Drupal\MannequinDateFormatter;
use LastCall\Mannequin\Drupal\Drupal\MannequinDrupalTwigExtension;
use LastCall\Mannequin\Drupal\Drupal\MannequinRenderer;
use LastCall\Mannequin\Drupal\Drupal\MannequinThemeManager;
use LastCall\Mannequin\Drupal\Drupal\MannequinUrlGenerator;
use LastCall\Mannequin\Drupal\Twig\Loader\FallbackLoader;
use LastCall\Mannequin\Twig\Driver\SimpleTwigDriver;

/**
 * Creates a Drupal-like Twig_Environment.
 *
 * This driver adds the following above and beyond the simple driver:
 *   * Drupal Twig functions.
 *   * Drupal Twig namespaces (modules and themes)
 */
class DrupalTwigDriver extends SimpleTwigDriver
{
    private $drupalRoot;
    private $discovery;

    private $detectedNamespaces = null;

    public function __construct(string $drupalRoot, ExtensionDiscovery $discovery, array $twigOptions = [], array $namespaces = [], array $fallbackExtensions = [])
    {
        if (!is_dir($drupalRoot)) {
            throw new \InvalidArgumentException(sprintf('Drupal root %s does not exist', $drupalRoot));
        }
        if (!file_exists(sprintf('%s/core/includes/bootstrap.inc', $drupalRoot))) {
            throw new \InvalidArgumentException(sprintf('Directory %s does not look like a Drupal installation', $drupalRoot));
        }
        parent::__construct($drupalRoot, $twigOptions, $namespaces);

        $this->drupalRoot = $drupalRoot;
        $this->discovery = $discovery;
        $this->fallbackExtensions = $fallbackExtensions;
    }

    protected function initialize(\Twig\Environment $twig)
    {
        parent::initialize($twig);
        $extension = new MannequinDrupalTwigExtension(
            $this->getRenderer(),
            $this->getGenerator(),
            $this->getThemeManager(),
            $this->getDateFormatter()
        );
        $twig->addExtension($extension);
    }

    protected function createLoader()
    {
        $loader = parent::createLoader();
        if ($this->fallbackExtensions) {
            $fallbackExtensions = array_intersect_key($this->getDrupalExtensions(), array_flip($this->fallbackExtensions));
            $fallbackPaths = [];
            foreach ($fallbackExtensions as $extension) {
                if (false !== $dir = $this->getExtensionTemplateDirectory($extension)) {
                    $fallbackPaths[] = $dir;
                }
            }
            $loader = new \Twig\Loader\ChainLoader([
                $loader,
                new FallbackLoader($fallbackPaths, $this->drupalRoot),
            ]);
        }

        return $loader;
    }

    protected function getNamespaces(): array
    {
        if (null === $this->detectedNamespaces) {
            $this->detectedNamespaces = [];
            foreach ($this->getDrupalExtensions() as $key => $extension) {
                if (false !== $dir = $this->getExtensionTemplateDirectory($extension)) {
                    $this->detectedNamespaces[$key][] = $dir;
                }
            }
        }

        return parent::getNamespaces() + $this->detectedNamespaces;
    }

    /**
     * @return \Drupal\Core\Extension\Extension[]
     */
    private function getDrupalExtensions()
    {
        require_once sprintf('%s/core/includes/bootstrap.inc', $this->drupalRoot);

        return $this->exts = array_merge(
            $this->discovery->scan('module', false),
            $this->discovery->scan('theme', false)
        );
    }

    private function getExtensionTemplateDirectory(Extension $extension)
    {
        $dir = sprintf('%s/templates', $extension->getPath());
        if (is_dir(sprintf('%s/%s', $this->drupalRoot, $dir))) {
            return $dir;
        }

        return false;
    }

    private function getRenderer(): RendererInterface
    {
        return new MannequinRenderer();
    }

    private function getGenerator(): UrlGeneratorInterface
    {
        return new MannequinUrlGenerator();
    }

    private function getThemeManager(): ThemeManagerInterface
    {
        return new MannequinThemeManager();
    }

    private function getDateFormatter(): DateFormatterInterface
    {
        return new MannequinDateFormatter();
    }
}
