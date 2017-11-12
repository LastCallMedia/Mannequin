# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Upcoming
## 1.0.7
## 1.0.6
## 1.0.5
### Added
- (Twig, Drupal) Allow additional namespaces to be passed to Twig and Drupal extensions - [#89](https://github.com/LastCallMedia/Mannequin/issues/89)
- (Twig, Drupal) Use the Twig auto_reload option all the time - [#94](https://github.com/LastCallMedia/Mannequin/issues/94)

## 1.0.4
## 1.0.3
### Fixed
- (Twig) Remove reference to Twig\Extension\InitRuntimeInterface from MannequinExtension.
- (Twig) Do not stop discovery process for a template that fails to load [#76](https://github.com/LastCallMedia/Mannequin/issues/76)

## 1.0.2
## 1.0.1
### Added
- (Meta,Core,Html,Drupal,Twig,Site,Ui) Started changelog, mirrored out to components on release.
- (Twig,Drupal) Parse Twig comments for @Component metadata comments.

### Deprecated
- (Twig,Drupal) Twig component metadata using the componentinfo block has been deprecated due to issues with guarded blocks in child templates.  Please use the new comment syntax instead.

## [1.0.0] - 2017-10-04
### Added
- (Core,Html,Drupal,Twig,Site,Ui) Initial release
