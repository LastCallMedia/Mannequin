<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Discovery;

use LastCall\Mannequin\Twig\Discovery\AbstractTwigDiscovery;
use Symfony\Component\Finder\Finder;

class DrupalExtensionTwigDiscovery extends AbstractTwigDiscovery
{
    private $drupalRoot;

    private $extensions = [];

    private $loader;

    private $prefix;

    public function __construct(
        string $drupal_root,
        array $extensions,
        \Twig_LoaderInterface $loader,
        string $prefix = 'drupal'
    ) {
        $this->drupalRoot = $drupal_root;
        $this->extensions = $extensions;
        $this->loader = $loader;
        $this->prefix = $prefix;
    }

    protected function getLoader(): \Twig_LoaderInterface
    {
        return $this->loader;
    }

    protected function getPrefix(): string
    {
        return $this->prefix;
    }

    protected function getNames(): array
    {
        $names = [];
        foreach ($this->extensions as $extension) {
            $names = array_merge($names, $this->getExtensionNames($extension));
        }

        return $names;
    }

    private function getExtensionNames($extension): array
    {
        $path = drupal_get_path('theme', $extension) ?: drupal_get_path(
            'module',
            $extension
        );
        if (!$path) {
            throw new \RuntimeException(
                sprintf('Unable to determine a path for %s', $extension)
            );
        }

        $finder = Finder::create()
            ->files()
            ->name('*.html.twig')
            ->in(sprintf('%s/%s/templates', $this->drupalRoot, $path));

        $names = [];
        foreach ($finder as $fileInfo) {
            $names[] = sprintf(
                '@%s/%s',
                $extension,
                $fileInfo->getRelativePathname()
            );
        }

        return $names;
    }
}
