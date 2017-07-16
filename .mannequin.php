<?php

use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Html\HtmlExtension;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

$twigFinder = Finder::create()
  ->files()
  ->in(__DIR__.'/src/Twig/Tests/Resources');

$htmlFinder = Finder::create()
  ->files()
  ->in(__DIR__.'/src/Html/Tests/Resources');

$twig = new TwigExtension([
  'finder' => $twigFinder,
  'twig_paths' => [
    '__main__' => [__DIR__.'/src/Twig/Tests/Resources']
  ]
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
