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

Next, configure and add one of the [available extensions](index.md#Extensions) to your configuration object like this:

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