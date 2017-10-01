<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig;

/**
 * Maps an absolute file path back to a name that Twig will know it by.
 *
 * Used to convert SplFileInfo objects returned by a Finder to usable template
 * names.
 */
class TemplateNameMapper
{
    private $twigRoot;
    private $namespaces = [];
    private $_map;

    const MAIN_NAMESPACE = '__main__';

    public function __construct(string $twigRoot)
    {
        $this->twigRoot = $twigRoot;
        $this->addNamespace(self::MAIN_NAMESPACE, $twigRoot);
    }

    public function addNamespace($namespace, $paths)
    {
        // Destroy the lookup map and cache.  It will be recreated.
        $this->_map = $this->_cache = null;
        $this->namespaces[$namespace] = [];
        foreach ((array) $paths as $path) {
            $this->namespaces[$namespace][] = rtrim($path, '/\\');
        }
    }

    /**
     * Lookup the possible twig names that correspond with a given filename.
     *
     * This method is performance-sensitive.
     *
     * @param $filename
     *
     * @return mixed
     */
    public function getTemplateNamesForFilename($filename)
    {
        $map = $this->getMap();
        $matching = array_filter(array_keys($map), function ($path) use ($filename) {
            return $filename[0] === $path[0] && 0 === strpos($filename, $path);
        });

        $templateNames = [];
        foreach ($matching as $match) {
            $namespace = $map[$match];
            $templateNames[] = self::MAIN_NAMESPACE === $namespace
                ? ltrim(substr($filename, strlen($match)), '/\\')
                : '@'.$namespace.substr($filename, strlen($match));
        }

        return $templateNames;
    }

    public function __invoke($templateNameOrFilename)
    {
        if ($templateNameOrFilename instanceof \SplFileInfo) {
            return $this->getTemplateNamesForFilename($templateNameOrFilename->getPathname());
        }

        return $templateNameOrFilename;
    }

    private function getMap()
    {
        if (!isset($this->_map)) {
            $this->_map = [];
            foreach ($this->namespaces as $namespace => $paths) {
                foreach ($paths as $path) {
                    if (!$this->isAbsolutePath($path)) {
                        $path = $this->twigRoot.'/'.$path;
                    }
                    $this->_map[$path] = $namespace;
                }
            }
        }

        return $this->_map;
    }

    private function isAbsolutePath($file)
    {
        return strspn($file, '/\\', 0, 1)
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === substr($file, 1, 1)
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
            ;
    }
}
