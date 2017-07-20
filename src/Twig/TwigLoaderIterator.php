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
            list($nsRegex, $nameRegex) = $this->convertGlob($glob);
            foreach ($this->matchingNamespaces($nsRegex) as $ns) {
                $finder = Finder::create()
                    ->files()
                    ->name($nameRegex)
                    ->in($this->loader->getPaths($ns));
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

    private function convertGlob(string $glob): array
    {
        if (strpos($glob, '@') === 0) {
            $parts = explode('/', $glob, 1);

            return [
                Glob::toRegex($parts[0]),
                Glob::toRegex($parts[1] ?? '*'),
            ];
        } else {
            // This is an un-namespaced glob.
            return [\Twig_Loader_Filesystem::MAIN_NAMESPACE, Glob::toRegex($glob)];
        }
    }

    private function matchingNamespaces($regex)
    {
        if ($regex === \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
            return [$regex];
        }

        return array_filter($this->loader->getNamespaces(), function ($ns) use ($regex) {
            return preg_match($regex, $ns);
        });
    }
}
