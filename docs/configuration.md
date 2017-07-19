Configuration
=============

Mannequin is configured for each project by creating a .mannequin.php file in the project root.  This file must return a `LastCall\Mannequin\Core\MannequinConfig` object.


```php
use LastCall\Mannequin\Core\MannequinConfig;
$config = MannequinConfig::create([
  'styles' => [/* URLs for stylesheets that will be available to every pattern*/]
  'scripts' => [/* URLs for javascripts that will be available to every pattern*/]
]);

return $config;
```

Next, you will choose one or more extensions to use.  The extensions that are currently available:

* [*HTML Extension*](https://github.com/LastCallMedia/Mannequin-Html) - Display static HTML files as Mannequin patterns.
* [*Twig Extension*](https://github.com/LastCallMedia/Mannequin-Twig) - Display Twig templates as Mannequin patterns.
* [*Drupal Extension*](https://github.com/LastCallMedia/Mannequin-Drupal) - Display Drupal 8 Twig templates as Mannequin patterns.

Configure and add the extension to your configuration object like this:

```php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Html\HtmlExtension;

$html = new HtmlExtension([/* HTML extension configuration goes here... */);

$config = MannequinConfig::create([
  'styles' => [/* URLs for stylesheets that will be available to every pattern*/]
  'scripts' => [/* URLs for javascripts that will be available to every pattern*/]
]);

$config->addExtension($html);

return $config;
```