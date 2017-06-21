<?php
/**
 * Plugin Name: WP Mautic
 * Plugin URI: https://github.com/mautic/mautic-wordpress
 * Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
 * Version: 2.0.4
 * Author: Mautic community
 * Author URI: http://mautic.org
 * Text Domain: mautic-wordpress
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
	include_once( VPMAUTIC_PLUGIN_DIR . '/options.php' );

	add_options_page(
		__( 'WP Mautic Settings', 'mautic-wordpress' ),
		__( 'WPMautic', 'mautic-wordpress' ),
		'manage_options',
		'wpmautic',
		'wpmautic_options_page'
	);
}

/**
 * Settings Link in the ``Installed Plugins`` page
 *
 * @param  array  $links array of plugin action links.
 * @param  string $file Path to the plugin file relative to the plugins directory.
 *
 * @return array
 */
function wpmautic_plugin_actions( $links, $file ) {
	if ( plugin_basename( VPMAUTIC_PLUGIN_FILE ) === $file && function_exists( 'admin_url' ) ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=wpmautic' ),
			__( 'Settings' )
		);
		// Add the settings link before other links.
		array_unshift( $links, $settings_link );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'wpmautic_plugin_actions', 10, 2 );

/**
 * Retrieve one of the wpmautic options but sanitized
 *
 * @param  string $option Option name to be retrieved (base_url, script_location).
 * @param  string $default Default option value return if not exists.
 *
 * @return string|array
 *
 * @throws InvalidArgumentException Thrown when the option name is not given.
 */
function wpmautic_option( $option, $default = null ) {
	$options = get_option( 'wpmautic_options' );

	switch ( $option ) {
		case 'script_location':
			return ! isset( $options[ $option ] ) ? 'header' : $options[ $option ];
		case 'fallback_activated':
			return isset( $options[ $option ] ) ? (bool) $options[ $option ] : true;
		case 'mautic_field_name':
		case 'wpmautic_tracking_user_field':
		case 'wpmautic_tracking_meta_field':
			return is_null( $options[ $option ] ) ? array() : $options[ $option ];
		default:
			if ( ! isset( $options[ $option ] ) ) {
				if ( isset( $default ) ) {
					return $default;
				}

				throw new InvalidArgumentException( 'You must give a valid option name !' );
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
	$script_location = wpmautic_option( 'script_location' );
	if ( 'header' === $script_location ) {
		add_action( 'wp_head', 'wpmautic_inject_script' );
	} else {
		add_action( 'wp_footer', 'wpmautic_inject_script' );
	}

	if ( true === wpmautic_option( 'fallback_activated', false ) ) {
		add_action( 'wp_footer', 'wpmautic_inject_noscript' );
	}
}

/**
 * Writes Tracking JS to the HTML source
 *
 * @return void
 */
function wpmautic_inject_script() {
	$base_url = wpmautic_option( 'base_url', '' );
	if ( empty( $base_url ) ) {
		return;
	}

	$user_query = wpmautic_get_user_query();
	$extra_info = '';
	if ( null !== $user_query ) {
		$extra_info = ', ' . wp_json_encode( $user_query );
	}

	?>
	<script type="text/javascript">(function (w, d, t, u, n, a, m) {
			w['MauticTrackingObject'] = n;
			w[n] = w[n] || function () {
				(w[n].q = w[n].q || []).push(arguments)
			}, a = d.createElement(t), m = d.getElementsByTagName(t)[0];
			a.async = 1;
			a.src = u;
			m.parentNode.insertBefore(a, m)
		})(window,document,'script','<?php echo esc_url( $base_url ); ?>/mtc.js','mt');
		mt('send', 'pageview'<?php echo $extra_info; ?>);</script>
	<?php
}

/**
 * Writes Tracking image fallback to the HTML source
 * This is a separated function because <noscript> tags are not allowed in header !
 *
 * @return void
 */
function wpmautic_inject_noscript() {
	$base_url = wpmautic_option( 'base_url', '' );
	if ( empty( $base_url ) ) {
		return;
	}

	global $wp;

	$url_query = wpmautic_get_url_query();
	$payload   = rawurlencode( base64_encode( serialize( $url_query ) ) );
	?>
	<noscript>
	<img src="<?php echo esc_url( $base_url ); ?>/mtracking.gif?d=<?php echo esc_attr( $payload ); ?>" style="display:none;" alt=""/>
	</noscript><?php
}

/**
 * Builds and returns additional data for URL query
 *
 * @return array
 */
function wpmautic_get_url_query() {
	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

	$attrs               = array();
	$attrs['page_url']   = $current_url;
	$attrs['page_title'] = function_exists( 'wp_get_document_title' )
		? wp_get_document_title()
		: wp_title( '&raquo;', false );
	$attrs['language']   = get_locale();
	$attrs['referrer']   = function_exists( 'wp_get_raw_referer' )
		? wp_get_raw_referer()
		: null;
	if ( false === $attrs['referrer'] ) {
		$attrs['referrer'] = $current_url;
	}

	return $attrs;
}

/**
 * Adds the user email, and other known elements about the user
 * to the tracking pixel.
 *
 * @return array
 */
function wpmautic_get_user_query() {

	$attrs = array();

	if ( is_user_logged_in() ) {

		$current_user   = wp_get_current_user();
		$attrs['email'] = $current_user->user_email;

		$user_fields = wpmautic_option( 'wpmautic_tracking_user_field' );
		$meta_fields = wpmautic_option( 'wpmautic_tracking_meta_field' );
		$field_name  = wpmautic_option( 'mautic_field_name' );

		foreach ( $user_fields as $key => $value ) {
			if ( $value == 'on' && strlen( $field_name[ $key ] ) > 0 && strlen( $current_user->$key ) > 0 ) {
				$attrs[ $field_name[ $key ] ] = $current_user->$key;
			}
		}

		foreach ( $meta_fields as $key => $value ) {
			if ( $value == 'on' && strlen( $field_name[ $key ] ) > 0 && strlen( get_user_meta( $current_user->ID, $key, true ) ) > 0 ) {
				$attrs[ $field_name[ $key ] ] = get_user_meta( $current_user->ID, $key, true );
			}
		}

		return $attrs;
	}

	return null;
}
