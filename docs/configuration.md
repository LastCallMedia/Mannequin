Configuration
=============

Mannequin is configured for each project by creating a .mannequin.php file in the project root.  This file must return a `LastCall\Mannequin\Core\MannequinConfig` object.


```php
use LastCall\Mannequin\Core\MannequinConfig;

$config = MannequinConfig::create()
    ->setGlobalCss([
      /* URLs or file paths for CSS */
    ])
    ->setGlobalJs([
      /* URLs or file paths for JS */
    );

return $config;
```

Next, configure and add one of the [available extensions](index.md#Extensions) to your configuration object like this:

```php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Html\HtmlExtension;

$html = new HtmlExtension([/* HTML extension configuration goes here... */);

$config = MannequinConfig::create()
    ->setGlobalCss([/* ... */])
    ->setGlobalJs([/* ... */]);
    ->addExtension($html);

return $config;
```