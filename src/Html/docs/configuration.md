---
title: Configuration
description: Configuration reference for Mannequin HTML Extension
---

Mannequin configuration lives in the `.mannequin.php` at the root of your project.  You should create this file, starting from the following example:

[@see config](../demo/.mannequin.php#L23-50)

## HTML Configuration
The `HtmlExtension` is what tells Mannequin how to access your Twig templates.  The mandatory arguments are the `finder` and the `twig_root`, but you can pass in `twig_options` as well:
```php
<?php

$twigExtension = new HtmlExtension([
    // A Symfony Finder object.
    'finder' => $htmlFiles,
    // The path to your 'root' directory. This is used
    // to convert absolute paths into relative ones.
    'root' => __DIR__
]);
```
For more documentation on the Finder, see the [Symfony Finder documentation](https://symfony.com/doc/current/components/finder.html).

## Mannequin Config
The `MannequinConfig` class handles configuration for Mannequin in general (the non-Drupal parts).  The configuration has a number of methods you can use to define your setup:

```php
<?php

$config = MannequinConfig::create();

// Add an extension to the Mannequin configuration:
$config->addExtension($drupalExtension);

// Set the CSS files that are used for every component.  CSS can be referenced
// using a relative path, in which case it will be looked up
// relative to your .mannequin.php, or an absolute URL.
$config->setGlobalCss([
    'themes/mytheme/css/theme.css',
    'http://example.com/theme.css',
]);

// Set the JS files that are used for every component.  JS can be referenced
// using a relative path, in which case it will be looked up
// relative to your .mannequin.php, or an absolute URL.
$config->setGlobalJs([
    'themes/mytheme/js/theme.js',
    'http://example.com/theme.js'
]);
```
