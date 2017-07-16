<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Mapper;

class FilesystemLoaderMapper
{
    private $paths = [];

    public function __construct(array $paths = [])
    {
        foreach ($paths as $namespace => $namespacePaths) {
            foreach ($namespacePaths as $path) {
                $this->addPath($path, $namespace);
            }
        }
    }

    public function addPath(
        $path,
        $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE
    ) {
        $this->paths[$namespace][] = $path;
    }

    public function __invoke($filename)
    {
        $discoveredNames = [];
        foreach ($this->paths as $name => $paths) {
            foreach ($paths as $path) {
                if (strpos($filename, $path) === 0) {
                    $templateName = ltrim(
                        substr($filename, strlen($path)),
                        '/'
                    );
                    $namespaceName = $name === \Twig_Loader_Filesystem::MAIN_NAMESPACE ? '' : sprintf(
                        '@%s/',
                        $name
                    );
                    $discoveredNames[] = $namespaceName.$templateName;
                }
            }
        }

        if (!empty($discoveredNames)) {
            return $discoveredNames;
        }
        throw new \RuntimeException(
            sprintf('%s does not exist in any known namespace', $filename)
        );
    }
}
