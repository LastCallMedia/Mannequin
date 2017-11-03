---
title: Configuration
description: Setting up your project.
---
The `.mannequin.php` file tells Mannequin what to do with your project.  Once you create this file, you will add any [Extensions](/extensions) you need, and any CSS/JS needed by your components.  You must return a `MannequinConfig` object from the .mannequin.php file.

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

In this example, we use the `setGlobalJs` and `setGlobalCss` methods to add assets that we want included in every template.  You can either pass in URLs or relative file paths here.  File paths should be relative to your config's `docroot`, which defaults to the directory .mannequin.php is in.  
Also in this example, we add a Symfony Finder object containing files in the `dist` directory to the configuration using the `setAssets` method.  This informs Mannequin that these assets should be packaged into any snapshots it generates.  You don't need to do this to use the live development server, but if you forget, your CSS and Javascript will be missing when you take a snapshot.
 
## Extensions

Without extensions, your configuration file is useless.  Extensions are what allow you to reference components.  Extensions are added using the `addExtension()` method, like so:
```php
# .mannequin.php 
...

return MannequinConfig::create()
  ->addExtension(new HtmlExtension());
```

See the [documentation for extensions](../extensions.md) for more information on how to use your extension.