<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Discovery;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Html\Component\HtmlComponent;
use Symfony\Component\Finder\SplFileInfo;

class HtmlDiscovery implements DiscoveryInterface
{
    use IdEncoder;

    private $files;

    /**
     * HtmlDiscovery constructor.
     *
     * @param \Traversable|array $files
     */
    public function __construct(\Traversable $files)
    {
        $this->files = $files;
    }

    public function discover(): ComponentCollection
    {
        $components = [];
        foreach ($this->files as $file) {
            if ($file instanceof SplFileInfo) {
                $name = $file->getRelativePathname();
                $fileInfo = $file;
            } elseif ($file instanceof \SplFileInfo) {
                $name = $file->getPathname();
                $fileInfo = $file;
            } else {
                $name = (string) $file;
                $fileInfo = new \SplFileInfo($name);
            }

            $component = new HtmlComponent(
                $this->encodeId($name),
                [$name],
                $fileInfo
            );
            $component->setName($name);
            $components[] = $component;
        }

        return new ComponentCollection($components);
    }
}
