<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Twig\TwigExtension;
use LastCall\Mannequin\Html\HtmlExtension;
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
    ->in(__DIR__.'/templates')
    ->name('*.twig');

$assetFinder = Finder::create()
    ->files()
    ->in(__DIR__.'/css');

/**
 * Create the TwigExtension object.
 */
$twigExtension = new TwigExtension([
    'finder' => $twigFinder,
    'twig_root' => __DIR__.'/templates',
]);

/**
 * Create a finder to search and list the static HTML files.
 */
$htmlFinder = Finder::create()
    ->files()
    ->in(__DIR__.'/static')
    ->name('*.html');

$htmlExtension = new HtmlExtension([
    'files' => $htmlFinder,
    'root' => __DIR__.'/static',
]);

/*
 * Create and return the configuration.  Don't forget to return it!
 */
return MannequinConfig::create()
    ->setGlobalJs(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'])
    ->setGlobalCss(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/css/foundation.css'])
    ->setAssets($assetFinder)
    ->addExtension($htmlExtension)
    ->addExtension($twigExtension);
