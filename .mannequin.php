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
use LastCall\Mannequin\Core\Ui\LocalUi;
use LastCall\Mannequin\Html\HtmlExtension;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

$htmlFinder = Finder::create()
  ->files()
  ->name('*.html')
  ->in(__DIR__.'/src/Html/demo/html');

$twigFinder = Finder::create()
    ->files()
    ->name('*.twig')
    ->in(__DIR__.'/src/Twig/demo/templates');

$twig = new TwigExtension([
    'finder' => $twigFinder,
    'twig_root' => __DIR__.'/src/Twig/demo/templates',
    'twig_options' => [
        'auto_reload' => true,
    ],
]);
$html = new HtmlExtension([
    'files' => $htmlFinder,
    'root' => __DIR__.'/src/Html/demo/html',
]);

$config = MannequinConfig::create([
    // Note: This setting should not be needed in your Mannequin config.
    // it is only necessary here because we need to use the bleeding-edge
    // version of the UI.
    'ui' => new LocalUi(__DIR__.'/ui'),
])
    ->setGlobalJs(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'])
    ->setGlobalCss(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/css/foundation.min.css'])
    ->addExtension($html)
    ->addExtension($twig);

return $config;
