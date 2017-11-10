---
title: Configuration
description: Configuration reference for Mannequin HTML Extension
---
The `HtmlExtension` accepts the following configuration options:
```php
use LastCall\Mannequin\Html\HtmlExtension;
use Symfony\Component\Finder\Finder;

$extension = HtmlExtension::create([
  // An array, or traversable containing absolute paths
  // to component .html files.  Usually a Symfony Finder
  // object.
  'files' => Finder::create(),
  // A directory beneath which all templates live.
  // Typically the current directory.
  'root' => __DIR__,
]);
```