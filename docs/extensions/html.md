---
title: HTML
description: Display HTML files as Mannequin patterns.
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
    'files' => $htmlFinder,
    'root' => __DIR__
]);

return MannequinConfig::create()
  ->addExtension($htmlExtension);
```

## Configuration

The `HTMLExtension` only accepts the following configuration options:

| Key | Description |
| --- | ----------- |
| files | An array, or traversable containing absolute paths to component .html files. |
| root  | An absolute path, below which all templates exist.  Used to convert absolute component file paths into relative paths. |

## Demo

See [the demo](https://github.com/LastCallMedia/Mannequin/blob/master/demo/.mannequin.php) .mannequin.php file for an example of how to configure the HTML extension.  See [this directory](https://github.com/LastCallMedia/Mannequin/tree/master/demo/static) for example HTML files and metadata.
