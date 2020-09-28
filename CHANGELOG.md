# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [v2.4.0] - 2020-09-28
### Changes
- Update text to be more clear regarding tracking cookies injection when using
  shortcodes and tracking pixel.
- Create a specific `wpmautic_send` javascript function that can be called by
  third party scripts to start tracking when user accepts it (GDPR compliance).

## [v2.3.2] - 2020-03-09
### Changes
- Fix error in the `plugin_action_links` named call which to the filter to never being called.

## [v2.3.1] - 2020-03-09
### Changes
- We are now compatible with WordPress 5.4.*
- Our "settings" link in the "Plugins" screen is now only applied to WPMautic.

## [v2.3.0] - 2020-02-20
### Added
- Refine wording on the settings page, to be more meaningful.
- Introduce readme.txt generator to avoid manual manipulation during release.
- Add Makefile to simplify the command to run inside the project.
- Add a new `script_location` value to disable script rendering on page load (GDPR compliance).

### Changes
- We are now compatible with PHP 7.4 and WordPress 5.3.*

## [v2.2.2] - 2017-11-13
### Changed
- We are now compatible with PHP7.2 and WordPress 4.9.

## [v2.2.1] - 2017-08-24
### Changed
- Fix an escaping error when using HTML in Mautic Dynamic Content shortcode. Previously, the HTML code was escaped...
- Remove obsolete shortcode syntax from documentation and code comments. They are still handled and not marked deprecated at the moment.

## [v2.2.0] - 2017-08-07
### Changed
- Add compatibility with the new Mautic 2.9.1 video features. It allow to track video even when not linked to a form ID (https://github.com/mautic/mautic/pull/4438).

## [v2.1.1] - 2017-07-19
### Changed
- Update some labels which are not clear enough.

## [v2.1.0] - 2017-07-19
### Added
- Call translation on all labels, plugin is translation ready !
- Add a new function `wpmautic_get_tracking_attributes` which defines attributes to be sent through JS and Image trackers.
- Add a filter `wpmautic_tracking_attributes` to allow developers injecting custom attributes in trackers.
- Add the ability to track logged user (within an option)

### Changed
- Add valid text domain and start official translation process.

## [v2.0.4] - 2017-06-03
### Changed
- Hotfix release, the async attribute on form generator script blocks `document.write`.

## [v2.0.3] - 2017-06-02
### Changed
- Hotfix release, the option group wasn't valid.

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
