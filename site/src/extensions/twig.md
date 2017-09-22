---
title: Twig
description: Display Twig templates as Mannequin components.
collection: Extensions
---
The Twig extension allows you to reference Twig templates as Mannequin components.  You would use this extension if you have Twig templates in your application, and you want to theme them outside the context of the application.  For example, by rendering Twig templates using Mannequin, theming no longer requires data models, an application bootstrap, or even a database connection.

## Installation
Use Composer to require this package:
```bash
composer require lastcall/mannequin-twig
```

## Usage

Add this extension in your .mannequin.php file as follows:
```php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

$twigFinder = Finder::create()
    ->in(__DIR__.'/Resources/views')
    ->files()
    ->name('*.twig');
    
$twigExtension = new TwigExtension([
    'finder' => $htmlFinder,
    'twig_root' => __DIR__.'/Resources/views'
]);

return MannequinConfig::create()
  ->addExtension($twigExtension);
```

## Configuration

The `TwigExtension` accepts the following configuration options:

| Key | Description |
| --- | ----------- |
| finder | A [Symfony Finder](https://symfony.com/doc/current/components/finder.html) object that will search for the Twig template files you want to use as components. |
| twig_root | An absolute path to the base directory containing your Twig templates.  This will be used to create a Twig filesystem loader internally. |
| twig_options | An associative array of [options to pass to Twig](https://twig.symfony.com/api/2.x/Twig_Environment.html#method___construct).  Defaults to using a `cache` property of the Mannequin cache directory. | 

## Component Metadata

The `DrupalExtension` is able to read component metadata out of a special block directly in the Twig file.  See [Components](/docs/components) for more info.
