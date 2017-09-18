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

class HtmlDiscovery implements DiscoveryInterface
{
    use IdEncoder;

    private $files;

    /**
     * HtmlDiscovery constructor.
     *
     * @param \Traversable|array $files
     */
    public function __construct($files)
    {
        if (!is_array($files) && !$files instanceof \Traversable) {
            throw new \InvalidArgumentException('$files must be an array, or \Traversable.');
        }
        $this->files = $files;
    }

    public function discover(): ComponentCollection
    {
        $components = [];
        foreach ($this->files as $filenames) {
            // @todo: Clean this up and make it consistent with TwigDiscovery.
            if (!is_array($filenames)) {
                $filenames = [$filenames];
            }
            $filenames = array_map(
                function ($filename) {
                    return (string) $filename;
                },
                $filenames
            );

            $id = reset($filenames);
            $component = new HtmlComponent(
                $this->encodeId($id),
                $filenames,
                new \SplFileInfo($id)
            );
            $component->setName($id);
            $components[] = $component;
        }

        return new ComponentCollection($components);
    }
}
