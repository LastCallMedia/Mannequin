<?php
/**
 * Created by PhpStorm.
 * User: rbayliss
 * Date: 11/8/17
 * Time: 4:59 PM
 */

namespace LastCall\Mannequin\Drupal\Discovery;


use LastCall\Mannequin\Drupal\Component\DrupalTwigComponent;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;

/**
 * Extends TwigDiscovery to create Drupal components.
 */
class DrupalTwigDiscovery extends TwigDiscovery
{
    public function createComponent(string $name, array $aliases, \Twig_Environment $twig): TwigComponent
    {
        return new DrupalTwigComponent($name, $aliases, $twig);
    }

}