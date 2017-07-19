<?php

use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

/**
 * This is a Mannequin configuration file.
 *
 * It defines what Mannequin should do.  This one tells the system to use the
 * Twig extension to look for patterns in the templates directory.
 */

/**
 * Create a finder to search and list the template files for the TwigExtension.
 */
$twigFinder = Finder::create()
    ->files()
    ->in(__DIR__)
    ->path('templates')
    ->name('*.twig');

/**
 * Create the TwigExtension object.
 */
$twigExtension = new TwigExtension([
    'finder' => $twigFinder
]);

/**
 * Create and return the configuration.  Don't forget to return it!
 */
return MannequinConfig::create([
        'styles' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/css/foundation.css'],
        'scripts' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'],
    ])
    ->addExtension($twigExtension);
