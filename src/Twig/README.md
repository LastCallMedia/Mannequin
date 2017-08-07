Mannequin Twig
==============

[![Packagist](https://img.shields.io/packagist/v/lastcall/mannequin-twig.svg)](https://packagist.org/packages/lastcall/mannequin-twig)

Contains the Twig extension for [Mannequin](https://github.com/LastCallMedia/Mannequin). Please file any bug reports or feature requests to the [main repository](https://github.com/LastCallMedia/Mannequin).

When would I use this?
-----------------------
When you want to display Twig templates as Mannequin patterns.

Installing
----------
Install using composer:
```bash
composer require lastcall/mannequin-twig
```

Usage
-----
The Twig Extension must be registered on your Mannequin configuration. In your .mannequin.php:

```php
<?php
# .mannequin.php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\Finder\Finder;

$finder = Finder::create()
    ->in(__DIR__.'/templates')
    ->name('*.twig')
    ->files();

$twig = new TwigExtension([
    'finder' => $twigFinder,
    'twig_root' => __DIR__.'/templates'
]);

return MannequinConfig::create()
  ->addExtension($twig);
```

Configuration
-------------
The TwigExtension has the following configuration options:

| name | Description |
| ---- | ----------- |
| finder | A Symfony Finder object that lists the files you would like to use as patterns.  Optionally, any iterator containing Twig template names can be passed. |
| twig_root | The root path to your Twig templates. |