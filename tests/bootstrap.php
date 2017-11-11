<?php
/**
 * PHPUnit bootstrap script
 *
 * @package wpmautic\tests
 */

echo 'Welcome to the WordPress Mautic Test Suite' . PHP_EOL . PHP_EOL;

// Load plugin within wordpress.
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( 'mautic-wordpress/wpmautic.php' ),
);


// Allow to define where WordPress source is installed.
if ( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
    require rtrim( getenv( 'WP_DEVELOP_DIR' ), DIRECTORY_SEPARATOR ) . '/tests/phpunit/includes/bootstrap.php';
    // Or assert that we are inside WordPress plugins directory.
} else {
    require __DIR__ . '/../../../../tests/phpunit/includes/bootstrap.php';
}

require_once dirname(__FILE__).'/WPMauticTestCase.php';
