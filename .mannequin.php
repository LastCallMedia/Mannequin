<?php

use LastCall\Mannequin\Core\Config;
use LastCall\Mannequin\Html\Extension\HtmlExtension;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use Symfony\Component\Finder\Finder;

$twigFinder = Finder::create()
  ->files()
  ->in(__DIR__.'/twig/Tests/Resources');

$htmlFinder = Finder::create()
  ->files()
  ->in(__DIR__.'/html/Tests/Resources');

$twig = new TwigExtension([
  'finder' => $twigFinder,
  'twig_paths' => [
    '__main__' => [__DIR__.'/twig/Tests/Resources']
  ]
]);


$html = new HtmlExtension([
  'finder' => $htmlFinder,
]);

$config = Config::create([
  'styles' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css'],
  'scripts' => ['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.min.js'],
])
  ->addExtension($html)
  ->addExtension($twig);

return $config;
