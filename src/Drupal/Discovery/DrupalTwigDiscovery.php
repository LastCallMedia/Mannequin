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

use LastCall\Mannequin\Drupal\Component\DrupalTwigComponent;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use Twig\Environment;

/**
 * Extends TwigDiscovery to create Drupal components.
 */
class DrupalTwigDiscovery extends TwigDiscovery
{
    public function createComponent(string $name, array $aliases, Environment $twig): TwigComponent
    {
        return new DrupalTwigComponent(
            $this->encodeId($name),
            $aliases,
            $twig->load($name)->getSourceContext(),
            $twig
        );
    }
}
