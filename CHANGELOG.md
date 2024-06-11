# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.4]
### Fixed
- Additional checks for image sizes, to prevent PHP errors when image is not set

## [1.2.3]
### Fixed
- Check if ad is allowed to be shown also when the post is saved. This prevents some odd situstions.
- Fix the post status on ad get functions.

## [1.2.2]
### Fixed
- Do not override utms that are manually set to ad url

## [1.2.1]
### Fixed
- creation of empty show counter meta
- ad show status check hooked also to filter `update_show_status`

## [1.2.0]
### Added
- boolean filter `drsa_enable_campaigns` to toggle campaign taxonomy visibility
- `drsa_field_{$field}` filters to allow modifying cpt metabox field args
- allow multiple heights for same ad place

### Fixed
- showing `multiple` ad places, create empty show count when post gets updated
- truly randomise ad places with multiple ads allowed, only fallback to order by show count
- init the plugin codebase on `after_setup_theme` action
- logic behind how ad visibility is determined

## [1.1.5]
### Changed
- Only count ad view once it is visible in the viewport

### Added
- Filter to enable view count as ad end condition
- Filter to enable alternative images for ads
- Allow ad positions with multiple ads
- Show more specific ad data at the bottom of ad edit view

## [1.1.4]
### Fixed
- CMB2 dependency file names

## [1.1.3]
### Added
- Filter that allows chaning the cpt capability type

## [1.1.1]
### Fixed
- Change jQuery event handler function

## [1.1.0]
Finally after years, get the plugin out of beta.

### Changed
- js view and click traking method to allow multiple ads in one page, this should not affect to old usage cases but test before production _as always_
- campaigns to dot show inactive ads for the place anymore
- updated CMB2 dependency

### Fixed
- php warnings

### Added
- multiple more detailed hooks
