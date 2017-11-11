---
title: Configuration
description: Configuration for the Mannequin TWig extension.
---

The `TwigExtension` object accepts the following configuration options:

| Key | Description |
| --- | ----------- |
| finder | A [Symfony Finder](https://symfony.com/doc/current/components/finder.html) object that will search for the Twig template files you want to use as components. |
| twig_root | An absolute path to the base directory containing your Drupal installation.  This will be used to create a Twig filesystem loader internally. |
| twig_options | An associative array of [options to pass to Twig](https://twig.symfony.com/api/2.x/Twig_Environment.html#method___construct).  Defaults to using a `cache` property of the Mannequin cache directory. |

It also has the following methods to be used for configuration:
* `addTwigPath(string $namespace, string $path)` Adds an additional path to the Twig loader, under a specific namespace.  Use this method to add additional namespaces to the loader.  If you want to use components inside of the added namespace, make sure to add the paths to your `Finder` as well.

Example
-------
```php
// .mannequin.php

$extension = new TwigExtension([
  'finder' => Finder::create(),
  'drupal_root' => __DIR__,
  'twig_options' => [
    'debug' => true,
  ]
]);

// Add an additional namespace to the loader.
// Note: To use this namespace in Drupal, you would also need to register it there.
$extension->addTwigPath('atoms', __DIR__.'/themes/mytheme/atoms');
```