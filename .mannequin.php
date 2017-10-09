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
use LastCall\Mannequin\Html\HtmlExtension;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

$htmlFinder = Finder::create()
  ->files()
  ->name('*.html')
  ->in(__DIR__.'/demo/static');

$twigFinder = Finder::create()
    ->files()
    ->name('*.twig')
    ->in(__DIR__.'/demo/templates');

$assetFinder = Finder::create()
    ->files()
    ->in(__DIR__.'/demo/css');

$twig = new TwigExtension([
    'finder' => $twigFinder,
    'twig_root' => __DIR__.'/demo/templates',
    'twig_options' => [
        'auto_reload' => true,
    ],
]);
$html = new HtmlExtension([
    'files' => $htmlFinder,
    'root' => __DIR__,
]);

$config = MannequinConfig::create([
    'ui_path' => __DIR__.'/ui/build',
])
    ->setGlobalJs(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'])
    ->setGlobalCss(['demo/css/style.css'])
    ->setAssets($assetFinder)
    ->addExtension($html)
    ->addExtension($twig);

return $config;
