---
title: Configuration
description: Configuration for the Mannequin TWig extension.
---


Mannequin configuration lives in the `.mannequin.php` at the root of your project.  You should create this file, starting from the following example:

[@see config](../demo/.mannequin.php#L23-50)

## Twig Configuration
The `TwigExtension` is what tells Mannequin how to access your Twig templates.  The mandatory arguments are the `finder` and the `twig_root`, but you can pass in `twig_options` as well:
```php
<?php

$twigExtension = new TwigExtension([
    // A Symfony Finder object.
    'finder' => $drupalFinder,
    // The path to your Drupal root.
    'twig_root' => __DIR__,
    // Optional: An associative array of options
    // to pass to the Twig environment.
    'twig_options' => [
      'debug' => true
    ]
]);
```
For more documentation on the Finder, see the [Symfony Finder documentation](https://symfony.com/doc/current/components/finder.html).  For information on the `twig_options` array, see the [Twig documentation](https://twig.symfony.com/api/2.x/Twig_Environment.html#method___construct).

The `TwigExtension` also has a couple additional methods you can use.
```php
<?php
// Register a new Twig namespace so @atoms/X.html.twig loads the template
// in themes/mytheme/patterns/atoms/X.html.twig.  If you want to use
// templates from this namespace as components, be sure to add them to your
// finder as well.
$twigExtension->addTwigPath('atoms', 'themes/mytheme/patterns/atoms');
```

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
