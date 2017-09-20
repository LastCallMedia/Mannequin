<?php

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
        'auto_reload' => TRUE
    ]
]);
$html = new HtmlExtension([
  'finder' => $htmlFinder,
]);


$config = MannequinConfig::create([
  'ui' => new \LastCall\Mannequin\Core\Ui\LocalUi(__DIR__.'/ui'),
])
    ->setGlobalJs(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'])
    ->setGlobalCss(['demo/css/style.css'])
    ->setAssets($assetFinder)
    ->addExtension($html)
    ->addExtension($twig);

return $config;
