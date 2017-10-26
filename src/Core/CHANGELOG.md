# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Upcoming
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
- (Core) Start mannequin development server on 0.0.0.0 by default.
- (Core) Output error messages from the development server even when not using verbose.

## 1.0.2
### Fixed
- (Core) Fixed index.php in repository root will be served instead of live development server.

## 1.0.1
### Added
- (Meta,Core,Html,Drupal,Twig,Site,Ui) Started changelog, mirrored out to components on release.

## [1.0.0] - 2017-10-04
### Added
- (Core,Html,Drupal,Twig,Site,Ui) Initial release
