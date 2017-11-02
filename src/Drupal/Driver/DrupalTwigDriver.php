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
use Drupal\Core\Extension\ExtensionDiscovery;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use LastCall\Mannequin\Drupal\Drupal\MannequinDateFormatter;
use LastCall\Mannequin\Drupal\Drupal\MannequinDrupalTwigExtension;
use LastCall\Mannequin\Drupal\Drupal\MannequinRenderer;
use LastCall\Mannequin\Drupal\Drupal\MannequinThemeManager;
use LastCall\Mannequin\Drupal\Drupal\MannequinUrlGenerator;
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

    public function __construct(string $drupalRoot, ExtensionDiscovery $discovery, array $twigOptions = [], array $namespaces = [])
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
    }

    protected function initialize(\Twig_Environment $twig)
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

    protected function getNamespaces(): array
    {
        if (null === $this->detectedNamespaces) {
            require_once sprintf('%s/core/includes/bootstrap.inc', $this->drupalRoot);

            $this->detectedNamespaces = [];
            foreach ($this->discovery->scan('module', false) as $key => $extension) {
                $dir = sprintf('%s/templates', $extension->getPath());
                if (is_dir(sprintf('%s/%s', $this->drupalRoot, $dir))) {
                    $this->detectedNamespaces[$key][] = $dir;
                }
            }
            foreach ($this->discovery->scan('theme', false) as $key => $extension) {
                $dir = sprintf('%s/templates', $extension->getPath());
                if (is_dir(sprintf('%s/%s', $this->drupalRoot, $dir))) {
                    $this->detectedNamespaces[$key][] = $dir;
                }
            }
        }

        return parent::getNamespaces() + $this->detectedNamespaces;
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
