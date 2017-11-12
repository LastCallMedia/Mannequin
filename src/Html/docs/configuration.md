---
title: Configuration
description: Configuration reference for Mannequin HTML Extension
---
The `HtmlExtension` accepts the following configuration options:

| Key | Description |
| --- | ----------- |
| files | A [Symfony Finder](https://symfony.com/doc/current/components/finder.html) object that will search for the HTML files you want to use as components. |
| root | An absolute path to the base directory (typically the current directory). This will be used to convert absolute paths to relative ones.|

## Example
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