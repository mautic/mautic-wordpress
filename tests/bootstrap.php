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
    $path = rtrim( getenv( 'WP_DEVELOP_DIR' ), DIRECTORY_SEPARATOR ) . '/tests/phpunit/includes/bootstrap.php';
    if (!is_readable($path)) {
        $message = <<<ERROR
To run tests, you must define the 'WP_DEVELOP_DIR' with the path to your WordPress Development version checkout.
For example :
    export WP_DEVELOP_DIR="/home/development/wordpress-develop";

You can also specify the variable on the fly when running tests :
    WP_DEVELOP_DIR=/home/development/wordpress-develop vendor/bin/phpunit
ERROR;
        echo $message;
        exit(1);
    }

    require $path;
    // Or assert that we are inside WordPress plugins directory.
} else {
    require __DIR__ . '/../../../../tests/phpunit/includes/bootstrap.php';
}

require_once dirname(__FILE__).'/WPMauticTestCase.php';
require_once dirname(__FILE__).'/../wpmautic.php';
