---
title: Configuration
description: Configuration for the Mannequin Drupal extension.
---

Mannequin configuration lives in the `.mannequin.php` at the root of your project.  You should create this file, starting from the following example:
```php
<?php // .mannequin.php

use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Drupal\DrupalExtension;
use Symfony\Component\Finder\Finder;

// Describe where to find Drupal templates.
// See https://symfony.com/doc/current/components/finder.html
$drupalFinder = Finder::create()
    // Templates can live in your normal templates directory.
    ->in(__DIR__.'/themes/mytheme/templates')
    ->files()
    ->name('*.twig');
    
$drupalExtension = new DrupalExtension([
    'finder' => $drupalFinder,
    'drupal_root' => __DIR__,
]);

return MannequinConfig::create()
    ->addExtension($drupalExtension)
    ->setGlobalJs([
      // themes/mytheme/js/theme.js  
    ])
    ->setGlobalCss([
      // themes/mytheme/css/theme.css
    ]);
```

## Drupal Configuration
The `DrupalExtension` is what tells Mannequin how to access your Drupal template files.  The mandatory arguments are the `finder` and the `drupal_root`, but you can pass in `twig_options` as well:
```php
<?php

$drupalExtension = new DrupalExtension([
    // A Symfony Finder object.
    'finder' => $drupalFinder,
    // The path to your Drupal root.
    'drupal_root' => __DIR__,
    // An associative array of options to pass to the Twig environment.
    'twig_options' => [
      'debug' => true
    ]
]);
```
For more documentation on the Finder, see the [Symfony Finder documentation](https://symfony.com/doc/current/components/finder.html).  For information on the `twig_options` array, see the [Twig documentation](https://twig.symfony.com/api/2.x/Twig_Environment.html#method___construct).

The DrupalExtension also has a couple additional methods you can use.
```php
<?php
// Register a new Twig namespace so @atoms/X.html.twig loads the template
// in themes/mytheme/patterns/atoms/X.html.twig.  If you want to use
// templates from this namespace as components, be sure to add them to your
// finder as well.
$drupalExtension->addTwigPath('atoms', 'themes/mytheme/patterns/atoms');

// Set the Drupal extensions (themes or modules) that are searched for
// extend and include statements that don't use a namespace.  Eg:
// {% extends 'block.html.twig' %} looks for block.html.twig in the
// classy theme.  The default fallback extension is the Stable theme.
$drupalExtension->setFallbackExtensions(['classy']);
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
