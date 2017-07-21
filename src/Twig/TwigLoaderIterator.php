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

use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Glob;

class TwigLoaderIterator implements \IteratorAggregate
{
    private $loader;
    private $globs = [];

    public function __construct(\Twig_Loader_Filesystem $loader, $rootPath, array $globs = ['@*/*'])
    {
        $this->loader = $loader;
        $this->rootPath = $rootPath;
        $this->globs = $globs;
    }

    public function getIterator()
    {
        $outer = new \AppendIterator();
        foreach ($this->globs as $glob) {
            list($nsGlob, $nameGlob) = $this->explodeGlob($glob);
            foreach ($this->matchingNamespaces($nsGlob) as $ns) {
                $paths = $this->prefixPaths($this->loader->getPaths($ns));

                $finder = Finder::create()
                    ->files()
                    ->name($nameGlob)
                    ->in($paths);
                $inner = new MappingCallbackIterator($finder, function ($file) use ($ns) {
                    $filename = $file->getRelativePathname();
                    if ($ns !== \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
                        $filename = sprintf('@%s/%s', $ns, $filename);
                    }

                    return [$filename];
                });
                $outer->append($inner);
            }
        }

        return $outer;
    }

    private function explodeGlob($glob)
    {
        if (strpos($glob, '@') === 0) {
            return explode('/', $glob, 2);
        }

        return ['@'.\Twig_Loader_Filesystem::MAIN_NAMESPACE, $glob];
    }

    private function prefixPaths(array $paths)
    {
        return array_map(function ($path) {
            return $this->isAbsolutePath($path)
                ? $path
                : $this->rootPath.'/'.$path;
        }, $paths);
    }

    private function isAbsolutePath($file)
    {
        return strspn($file, '/\\', 0, 1)
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && substr($file, 1, 1) === ':'
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
            ;
    }

    public function matchingNamespaces($spec)
    {
        $namespaces = $this->loader->getNamespaces();

        if (strpos($spec, '@') === 0) {
            // This is a specifically scoped specification.
            $spec = substr($spec, 1);
        } else {
            $spec = \Twig_Loader_Filesystem::MAIN_NAMESPACE.'/'.$spec;
        }
        list($spec) = explode('/', $spec, 2);

        $regex = Glob::toRegex($spec);

        return array_filter($namespaces, function ($ns) use ($regex) {
            return preg_match($regex, $ns);
        });
    }
}
