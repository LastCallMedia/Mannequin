<?php

use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Html\HtmlExtension;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

$htmlFinder = Finder::create()
  ->files()
  ->name('*.html')
  ->in(__DIR__.'/demo/static');

$twig = new TwigExtension([
    'globs' => ['*'],
    'twig_root' => __DIR__.'/demo/templates',
]);
$html = new HtmlExtension([
  'finder' => $htmlFinder,
]);

$config = MannequinConfig::create([
  'ui' => new \LastCall\Mannequin\Core\Ui\LocalUi(__DIR__.'/ui/build'),
  'styles' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css'],
  'scripts' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.min.js'],
])
  ->addExtension($html)
  ->addExtension($twig);

return $config;
