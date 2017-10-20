# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Upcoming
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
