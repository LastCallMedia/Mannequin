---
title: Configuration
description: Setting up your project.
collection: Documentation
---
The `.mannequin.php` file tells Mannequin what to do with your project.  In this file, you will add any [Extensions](/extensions) you need, and any CSS/JS needed by your components.  You must return a `MannequinConfig` object from the .mannequin.php file.

```php
# .mannequin.php
use LastCall\Mannequin\Core\MannequinConfig;
    
return MannequinConfig::create();
```

## Assets

You will almost certainly need CSS and Javascript assets to go along with your components.  Mannequin does not handle compilation of assets, but it does allow you to reference them.

```php
# .mannequin.php
use LastCall\Mannequin\Core\MannequinConfig;
use Symfony\Component\Finder\Finder;

$assetFinder = Finder::create()
    ->files()
    ->in(__DIR__.'/dist');
    
return MannequinConfig::create()
    ->setGlobalJs(['https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.1/js/foundation.min.js'])
    ->setGlobalCss(['dist/css/style.css'])
    ->setAssets($assetFinder);
```

This example adds one javascript file and one CSS file to every component as it is rendered.  Because CSS and Javascript often need ancillary files like images, we have also specified a directory worth of assets that are made available using the `setAssets()` method.  Without specifying the assets, the CSS and Javscript files would not load.  When your component library is packed up for distribution, these assets will also be snapshotted and bundled up to go with the HTML.
 
## Extensions

Without extensions, your configuration file is useless.  Extensions are what allow you to reference components.  Extensions are added using the `addExtension()` method, like so:
```php
# .mannequin.php 
...

return MannequinConfig::create()
  ->addExtension(new HtmlExtension());
```

See the [documentation for extensions](/extensions) for more information on how to use your extension.