What is this?
-------------
Mannequin is a component based theming tool for the web.  It builds on the work of tools like [Pattern Lab](http://patternlab.io/), and [Fractal](http://fractal.build/).

Our Mission
-----------
1.  Front end development should be [loosely coupled](https://en.wikipedia.org/wiki/Loose_coupling) to backend development.  One should not block the other.
2.  Front end development should not happen in a silo, and it should not require copying files from one place to another.  We should be able to develop templates and associated assets wherever they will eventually be used (alongside or inside of the application).
3.  Front end assets are some of the most important targets for automated testing.  It should be easy to expose rendered output to tools that can run visual regression, accessibility, or other types of tests.
4.  Patterns/templates should be as self-documenting as possible.  The metadata for a pattern should live as close to the template itself as possible.

Getting Started
---------------

1. Choose the extension you would like to use, and install it.
2. Create a [.mannequin.php](configuration.md) file.
3. Start a live development server using vendor/bin/mannequin server.

Extensions
----------
While we would eventually like to provide support for all major frameworks and CMS systems, these are the extensions that are currently available.
* [*HTML Extension*](https://github.com/LastCallMedia/Mannequin-Html) - Display static HTML files as Mannequin patterns.
* [*Twig Extension*](https://github.com/LastCallMedia/Mannequin-Twig) - Display Twig templates as Mannequin patterns.
* [*Drupal Extension*](https://github.com/LastCallMedia/Mannequin-Drupal) - Display Drupal 8 Twig templates as Mannequin patterns.

Commands
--------

| Name | Description |
| ---: | :---------- |
| server | Start a web server for live pattern development|
| render | Render everything to static HTML | 
| debug  | Display information on patterns and variable types |

Further Reading
---------------
* [Configuration](configuration.md)
* [Patterns](patterns.md)
* [Extensions](extensions.md)
