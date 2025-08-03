<?php
/**
 * Plugin Name: WP Mautic
 * Plugin URI: https://github.com/mautic/mautic-wordpress
 * Contributors: mautic,hideokamoto,shulard,escopecz,dbhurley,macbookandrew,dogbytemarketing
 * Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
 * Version: 2.5.0
 * Requires at least: 4.6
 * Author: Mautic community
 * Author URI: http://mautic.org
 * Text Domain: wp-mautic
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package wp-mautic
 */

namespace Mautic\WP_Mautic;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Store plugin directory.
define( 'VPMAUTIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Store plugin main file path.
define( 'VPMAUTIC_PLUGIN_FILE', __FILE__ );

class WP_Mautic
{
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->load_dependencies();
  }
  
  /**
   * Load dependencies
   *
   * @return void
   */
  public function load_dependencies() {
    $dependency_loader = new Dependency_Loader();
    $dependency_loader->init();
  }

}

$wp_mautic = new WP_Mautic();
$wp_mautic->init();