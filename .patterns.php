<?php

use LastCall\Patterns\Core\Config;
use LastCall\Patterns\Html\Extension\HtmlExtension;
use LastCall\Patterns\Twig\Extension\TwigExtension;
use Symfony\Component\Finder\Finder;

$twig = new TwigExtension([
  'paths' => [__DIR__.'/twig/Tests/Resources']
]);
$html = new HtmlExtension();
$html->in([
  __DIR__.'/core/Tests/Resources',
  __DIR__.'/twig/Tests/Resources',
]);

$config = Config::create()
  ->addExtension($html)
  ->addExtension($twig)
  ->addStyles([
    'https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css'
  ])
  ->addScripts([
    'https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.min.js',
  ]);


$config->getFinder()->in([
  __DIR__.'/core/Tests/Resources',
  __DIR__.'/twig/Tests/Resources'
]);

return $config;
