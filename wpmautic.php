<?php
/**
 * Plugin Name: WP Mautic
 * Plugin URI: https://github.com/mautic/mautic-wordpress
 * Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
 * Version: 1.0.1
 * Author: Mautic community
 * Author URI: http://mautic.org
 * License: GPL2
 *
 * @package wpmautic
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

// Store plugin directory.
define( 'VPMAUTIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// Store plugin main file path.
define( 'VPMAUTIC_PLUGIN_FILE', __FILE__ );

add_action( 'admin_menu', 'wpmautic_settings' );
add_action( 'plugins_loaded', 'wpmautic_injector' );

include_once( VPMAUTIC_PLUGIN_DIR . '/shortcodes.php' );

/**
 * Declare option page
 */
function wpmautic_settings() {
	include_once( dirname( __FILE__ ) . '/options.php' );
	add_options_page( 'WP Mautic Settings', 'WPMautic', 'manage_options', 'wpmautic', 'wpmautic_options_page' );
}

/**
 * Settings Link in the ``Installed Plugins`` page
 *
 * @param  array  $links array of plugin action links.
 * @param  string $file  Path to the plugin file relative to the plugins directory.
 *
 * @return array
 */
function wpmautic_plugin_actions( $links, $file ) {
	if ( plugin_basename( VPMAUTIC_PLUGIN_FILE ) === $file && function_exists( 'admin_url' ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=wpmautic' ) . '">' . __( 'Settings' ) . '</a>';
		// Add the settings link before other links.
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links', 'wpmautic_plugin_actions', 10, 2 );

/**
 * Retrieve one of the wpmautic options but sanitized
 *
 * @param  string $option Option name to be retrieved (base_url, script_location)
 *
 * @return string
 *
 * @throws Exception
 */
function wpmautic_option( $option ) {
	$options = get_option( 'wpmautic_options' );

	switch ( $option ) {
		case 'script_location':
			return ! isset( $options['script_location'] ) ? 'header' : $options['script_location'];
		default:
			if ( ! isset( $options[ $option ] ) ) {
				throw new Exception( 'You must give a valid option name !' );
			}

			return $options[ $option ];
	}
}

/**
 * Apply JS tracking to the right place depending script_location.
 *
 * @return void
 */
function wpmautic_injector() {
	$options = get_option( 'wpmautic_options' );
	if ( ! isset( $options['script_location'] ) || 'header' === $options['script_location'] ) {
		add_action( 'wp_head', 'wpmautic_inject_script' );
	} else {
		add_action( 'wp_footer', 'wpmautic_inject_script' );
	}
}

/**
 * Writes Tracking JS to the HTML source
 *
 * @return void
 */
function wpmautic_inject_script() {
	$options = get_option( 'wpmautic_options' );
	$base_url = trim( $options['base_url'], " \t\n\r\0\x0B/" );
	if ( empty( $base_url ) ) {
		return;
	}

	?><script type="text/javascript">
	(function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
		w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
		m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
	})(window,document,'script','<?php echo esc_url( $base_url ); ?>/mtc.js','mt');

	mt('send', 'pageview');
</script>
	<?php
}

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @param  string $title Default title text for current view.
 * @param  string $sep Optional separator.
 *
 * @return string Filtered title.
 */
function wpmautic_wp_title( $title = '', $sep = '' ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= trim( wp_title( $sep, false ) );

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		/*
		 * translators: Pagination number
		 */
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );
	}

	return $title;
}
