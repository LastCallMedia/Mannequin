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
use Symfony\Component\Finder\Finder;

/**
 * This is a Mannequin configuration file.
 *
 * It defines what Mannequin should do.  This one tells the system to use the
 * Html extension to look for component in the html directory.
 */

/**
 * Create a finder to search and list the Twig template files.
 */
$twigTemplates = Finder::create()
    ->files()
    ->in(__DIR__.'/templates')
    ->name('*.twig');

$twigExtension = new TwigExtension([
    'finder' => $twigTemplates,
    'twig_root' => __DIR__,
]);

/*
 * Create and return the configuration.  Don't forget to return it!
 */
return MannequinConfig::create()
    // JS and CSS can either be local (relative paths from the root),
    // or remote (absolute URLs)
    ->setGlobalJs(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'])
    ->setGlobalCss(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/css/foundation.css'])
    ->addExtension($twigExtension);
