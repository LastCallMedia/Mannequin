---
title: HTML
description: Display HTML files as Mannequin patterns.
collection: Extensions
---
The HTML extension allows you to reference static HTML files as Mannequin components.  
This extension is particularly useful for:
* **Prototyping components**: Write the HTML and styling before actually making the component dynamic.
* **Generic Style Guides**: Use an HTML file to contain the "base" elements for your site to see how they look with the global styling applied.
* **Include-only components**: If you have components that you want to preview in your dynamic components, but don't need to write a full-blown template for them, write them as an HTML component, then reference them from the mannequin metadata.

## Installation
Use Composer to require this package:
```bash
composer require lastcall/mannequin-html
```

## Usage

Add this extension in your .mannequin.php file as follows:
```php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Html\HtmlExtension;
use Symfony\Component\Finder\Finder;

$htmlFinder = Finder::create()
    ->in(__DIR__)
    ->files()
    ->name('*.html');
    
$htmlExtension = new HtmlExtension([
    'finder' => $htmlFinder
]);

return MannequinConfig::create()
  ->addExtension($htmlExtension);
```

## Configuration

The `HTMLExtension` only accepts one configuration option, which is a [Symfony Finder](https://symfony.com/doc/current/components/finder.html) object that will search for the HTML files you want to use as components.