<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Discovery;

use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

/**
 * This class converts an iterable object of template names into TwigPattern
 * objects by using the Twig Loader.
 */
class TwigDiscovery implements DiscoveryInterface
{
    use IdEncoder;

    private $loader;

    private $names;

    public function __construct(\Twig_LoaderInterface $loader, $names)
    {
        $this->loader = $loader;
        if (!is_array($names) && !$names instanceof \Traversable) {
            throw new \InvalidArgumentException(
                '$names must be an array or a \Traversable object.'
            );
        }
        $this->names = $names;
    }

    public function discover(): PatternCollection
    {
        $patterns = [];
        foreach ($this->names as $names) {
            try {
                $primary = reset($names);
                $source = $this->loader->getSourceContext($primary);
                $pattern = new TwigPattern(
                    $this->encodeId($primary),
                    $names,
                    $source
                );
                $pattern->addTag('format', 'twig');
                $patterns[] = $pattern;
            } catch (\Twig_Error_Loader $e) {
                throw new UnsupportedPatternException(
                    sprintf('Unable to load %s', $primary), 0, $e
                );
            }
        }

        return new PatternCollection($patterns);
    }
}
