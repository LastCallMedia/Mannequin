---
title: Drupal
description: Display Drupal Twig templates as Mannequin components.
---
The Drupal extension allows you to reference Drupal 8 Twig templates as Mannequin components.  You would use this extension if you want to develop your components outside the context of Drupal.  For example, this would allow themers to begin implementing designs without requiring Content Types, Views, or even a functioning Drupal site.

This extension shares DNA with the [TwigExtension](../extensions/twig.md), but it configures Twig with additional Drupal specific behavior, such as:

* Loading of templates by namespace (@mytheme/mytemplate.htmltwig)
* Drupal twig extensions:
  * Dummy `t` and `trans`, and `format_date` filters
  * Dummy `url`, `path`, `file_link` render` functions
  * `link` and `file_url`, and `create_attribute` functions
  * `placeholder`, `drupal_escape`, `safe_join`, `without`, `clean_class`, `clean_id` filters
* Additional expressions to use in Sample declarations:
  * `attributes` - Creates an attribute object.

## Installation
Use Composer to require this package from your Drupal root:
```bash
composer require lastcall/mannequin-drupal
```

## Usage
Create or update your .mannequin.php file as follows:
```php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Drupal\DrupalExtension;
use Symfony\Component\Finder\Finder;

$drupalFinder = Finder::create()
    ->in(__DIR__.'/themes/mytheme')
    ->files()
    ->name('*.twig');
    
$drupalExtension = new DrupalExtension([
    'finder' => $drupalFinder,
    'drupal_root' => __DIR__,
]);

return MannequinConfig::create()
  ->addExtension($drupalExtension);
```

## Configuration

The `DrupalExtension` accepts the following configuration options:

| Key | Description |
| --- | ----------- |
| finder | A [Symfony Finder](https://symfony.com/doc/current/components/finder.html) object that will search for the Twig template files you want to use as components. |
| drupal_root | An absolute path to the base directory containing your Drupal installation.  This will be used to create a Twig filesystem loader internally. |
| twig_options | An associative array of [options to pass to Twig](https://twig.symfony.com/api/2.x/Twig_Environment.html#method___construct).  Defaults to using a `cache` property of the Mannequin cache directory. |

## Using Javascript

Since javascript for Drupal sites is typically triggered by Drupal core's `Drupal.attachBehaviors()` method, it can be helpful to add a few files directly from core to Mannequin's list of global javascript files in `.mannequin.php`:

```php
$config = MannequinConfig::create()
  ->addExtension($drupalExtension)
  ->setGlobalJs([
    '{path_to_docroot}/core/assets/vendor/domready/ready.min.js',
    '{path_to_docroot}/core/misc/drupal.js',
    '{path_to_docroot}/core/misc/drupal.init.js',
    // Any other javascript files you may need.
  ]);
```

Adding these files to the list of global javascript will cause Drupal's `attachBehaviors` method to get called on the document once it's ready, and allow your own behaviors to trigger when it's called.

## Component Metadata

The `DrupalExtension` is able to read component metadata out of a special comment directly in the Twig file.  See [Components](../docs/components.md) for more info.