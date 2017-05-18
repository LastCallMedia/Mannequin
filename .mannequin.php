<?php

use LastCall\Mannequin\Core\Config;
use LastCall\Mannequin\Html\Extension\HtmlExtension;
use LastCall\Mannequin\Twig\Extension\TwigExtension;

$twig = new TwigExtension([
  'paths' => [__DIR__.'/twig/Tests/Resources']
]);
$twig->in([
  __DIR__.'/twig/Tests/Resources',
]);
$html = new HtmlExtension();
$html->in([
  __DIR__.'/html/Tests/Resources',
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

return $config;
