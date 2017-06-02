# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added

### Changed

## [v2.0.4] - 2017-06-03
### Changed
- Hotfix release, the async attribute on form generator script blocks document.write.

## [v2.0.3] - 2017-06-02
### Changed
- Hotfix release, the option group wasn't valid !

## [v2.0.2] - 2017-06-02
### Added
- Make a solid test suite to check every plugin parts (settings, loading, injection)
- Add a new setting to activate tracking image as a fallback when javascript is disabled

### Changed
- Refactor shortcode handling and put everything in shortcodes.php.
- Clean old code from the repository (wpmautic_wp_title).

## [v2.0.1] - 2017-05-25
### Added
- Add a new option in settings screen to choose where the script is injected.
- Add new tests around script injection.

## [v2.0.0] - 2017-05-25
### Added
- Composer development requirement (squizlabs/php_codesniffer).
- Code sniffer configuration : phpcs.xml.
- Update code using the sniff.
- Add basic unit tests using PHPUnit.
- Activate continuous integration using Travis-CI (check .travis.yml file).

### Changed
- Use escape functions when printing data: esc_url.

## [v1.1.0] - 2017-05-06
### Added
- Add support for Gated video.
- Use JavaScript tracker instead of Gif image.

### Changed
- First release on the Github repository, will start changelog from that point...

## [v1.0.1] - 2015-11-05

## [v1.0.0] - 2015-03-02
### Changed
- First release made on the WordPress repository.
