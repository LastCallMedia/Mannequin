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
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

/**
 * This class converts an iterable object of template names into TwigPattern
 * objects by using the Twig driver.
 */
class TwigDiscovery implements DiscoveryInterface
{
    use IdEncoder;

    private $names;

    private $driver;

    public function __construct(TwigDriverInterface $driver, $names)
    {
        $this->driver = $driver;
        if (!is_array($names) && !$names instanceof \Traversable) {
            throw new \InvalidArgumentException(
                '$names must be an array or a \Traversable object.'
            );
        }
        $this->names = $names;
    }

    /**
     * {@inheritdoc}
     */
    public function discover(): PatternCollection
    {
        $twig = $this->driver->getTwig();
        $patterns = [];
        foreach ($this->names as $names) {
            try {
                $aliases = (array) $names;
                $name = reset($aliases);
                $pattern = new TwigPattern(
                    $this->encodeId($name),
                    $aliases,
                    $twig->load($name)->getSourceContext(),
                    $twig
                );
                $pattern->setName($name);
                $patterns[] = $pattern;
            } catch (\Twig_Error_Loader $e) {
                throw new UnsupportedPatternException(
                    sprintf('Unable to load %s', reset($names)), 0, $e
                );
            }
        }

        return new PatternCollection($patterns);
    }
}
