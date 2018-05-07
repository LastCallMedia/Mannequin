# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Upcoming
### Fixed
- (Twig) Undefined index error during testing of Twig extension on PHP 7.1.

## 1.0.7
### Added
- (Drupal) Concatenate arrays passed to render.
- (Drupal) Add fallback Twig loader to emulate Drupal's handling of unscoped include/extend statements by looking for templates in a specified module/theme - [#88](https://github.com/LastCallMedia/Mannequin/issues/88)
- (Drupal) Add default variables as in template_preprocess().

### Changed
- (Docs) Reorganize documentation to remove "common" pages.  Explain everything in the extensions.
- (Site) Redesign interior pages, add menu for navigatino within sections.
- (Site) Add "suggest a change" link.

## 1.0.6
## 1.0.5
### Added
- (Twig, Drupal) Allow additional namespaces to be passed to Twig and Drupal extensions - [#89](https://github.com/LastCallMedia/Mannequin/issues/89)
- (Twig, Drupal) Use the Twig auto_reload option all the time - [#94](https://github.com/LastCallMedia/Mannequin/issues/94)
- (Docs) Add command line documentation - [#97](https://github.com/LastCallMedia/Mannequin/issues/97)

### Changed
- (Core) Renamed `output-dir` option to `output` for `snapshot` command.

### Removed
- (Core) Remove unused `output-dir` option on start command.

### Fixed
- (Core) Path to docroot not being set using realpath (caused broken assets in snapshot)

## 1.0.4
### Added
- (Core) Docroot setting added to configuration using the `setDocroot` method on the `MannequinConfig` object.

### Fixed
- (Core) Version number should be displayed when running the console.
- (Core) Better error messages for missing/broken config files - [#75](https://github.com/LastCallMedia/Mannequin/issues/75).
- (Core) Add helpful tips for the server start command [#79](https://github.com/LastCallMedia/Mannequin/issues/79)
- (Core) Warning message when starting live development server with a config that has no extensions - [#80](https://github.com/LastCallMedia/Mannequin/issues/80)

## 1.0.3
### Added
- (Core) Pass logger to Discoverers that implement LoggerAwareInterface.

### Fixed
- (Twig) Remove reference to Twig\Extension\InitRuntimeInterface from MannequinExtension.
- (Core) Start mannequin development server on 0.0.0.0 by default.
- (Twig) Do not stop discovery process for a template that fails to load [#76](https://github.com/LastCallMedia/Mannequin/issues/76)
- (Core) Output error messages from the development server even when not using verbose.

## 1.0.2
### Added
- (Ui) Polyfills for Array.prototype.find and ES6 Map and Set

### Fixed
- (Core) Fixed index.php in repository root will be served instead of live development server.

## 1.0.1
### Added
- (Meta,Core,Html,Drupal,Twig,Site,Ui) Started changelog, mirrored out to components on release.
- (Twig,Drupal) Parse Twig comments for @Component metadata comments.

### Deprecated
- (Twig,Drupal) Twig component metadata using the componentinfo block has been deprecated due to issues with guarded blocks in child templates.  Please use the new comment syntax instead.

### Fixed
- (Demo) #65, $66 Fixed missing semicolon and incorrect version constraints.

## [1.0.0] - 2017-10-04
### Added
- (Core,Html,Drupal,Twig,Site,Ui) Initial release
