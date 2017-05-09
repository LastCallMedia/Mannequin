<?php

use LastCall\Patterns\Core\Config;
use LastCall\Patterns\Core\Extension\HtmlExtension;
use LastCall\Patterns\Twig\Extension\TwigExtension;

$config = Config::create()
  ->addExtension(new HtmlExtension())
  ->addExtension(new TwigExtension([
    'loader_paths' => [__DIR__.'/twig/Tests/Resources']
  ]))
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
