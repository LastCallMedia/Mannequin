<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Twig\Loader;

use Symfony\Component\Finder\Finder;

/**
 * This loader searches for unqualified template names in specific directories.
 *
 * Recurses into subdirectories searching for templates.  This is intended
 * to be a simplified simulation of Drupal's theme registry loader, which looks
 * up template paths against the stored theme registry.
 */
class FallbackLoader extends \Twig\Loader\FilesystemLoader
{
    private $protectedRoot;

    public function __construct($paths = [], $rootPath = null)
    {
        parent::__construct($paths, $rootPath);
        // Store $rootPath in a way we can access it.
        $this->protectedRoot = $rootPath;
    }

    public function findTemplate($name, $throw = true)
    {
        $name = $this->normalizeName($name);

        // Caching for found/not found.
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }
        if (isset($this->errorCache[$name])) {
            if (!$throw) {
                return false;
            }

            throw new \Twig\Error\LoaderError($this->errorCache[$name]);
        }

        // Skip processing for any names that include a directory separator or
        // a namepace.
        if (false === strpos($name, '/') && 0 !== strpos($name, '@')) {
            $paths = array_map(function ($path) {
                if (!$this->isAbsolute($path)) {
                    $path = $this->protectedRoot.'/'.$path;
                }

                return $path;
            }, $this->paths[$this::MAIN_NAMESPACE]);

            $finder = Finder::create()
                ->in($paths)
                ->files()
                ->name($name);

            foreach ($finder as $file) {
                return $this->cache[$name] = $file->getPathname();
            }
            $this->errorCache[$name] = sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths[self::MAIN_NAMESPACE]));
        } else {
            $this->errorCache[$name] = sprintf('Unable to find template "%s".', $name);
        }

        if (!$throw) {
            return false;
        }
        throw new \Twig\Error\LoaderError($throw->errorCache[$name]);
    }

    /**
     * Local duplicate of Twig_Loader_Filesystem::isAbsolutePath().
     */
    private function isAbsolute($path)
    {
        return strspn($path, '/\\', 0, 1)
            || (strlen($path) > 3 && ctype_alpha($path[0])
                && ':' === substr($path, 1, 1)
                && strspn($path, '/\\', 2, 1)
            )
            || null !== parse_url($path, PHP_URL_SCHEME)
            ;
    }
}
