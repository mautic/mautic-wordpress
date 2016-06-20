<?php
/**
 * Plugin Name: WP Mautic
 * Plugin URI: https://github.com/mautic/mautic-wordpress
 * Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
 * Version: 1.0.1
 * Author: Mautic community
 * Author URI: http://mautic.org
 * License: GPL2
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly
}

require_once __DIR__ . '/vendor/autoload.php';

// Store plugin directory
define( 'VPMAUTIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// Store plugin main file path
define( 'VPMAUTIC_PLUGIN_FILE', __FILE__ );

add_action('admin_menu', 'wpmautic_settings');
add_action('wp_footer', 'wpmautic_function');
add_shortcode('mauticform', 'wpmautic_shortcode');
add_shortcode('mauticcontent', 'wpmautic_dwc_shortcode');

function wpmautic_settings()
{
	include_once(dirname(__FILE__) . '/options.php');
	add_options_page('WP Mautic Settings', 'WPMautic', 'manage_options', 'wpmautic', 'wpmautic_options_page');
}

/**
 * Settings Link in the ``Installed Plugins`` page
 */
function wpmautic_plugin_actions( $links, $file ) {
	if( $file == plugin_basename( VPMAUTIC_PLUGIN_FILE ) && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=wpmautic' ) . '">' . __('Settings') . '</a>';
		// Add the settings link before other links
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links', 'wpmautic_plugin_actions', 10, 2 );

/**
 * Writes Tracking image to the HTML source of WP
 */
function wpmautic_function( $atts, $content = null )
{
	$options = get_option('wpmautic_options');
	$url_query = wpmautic_get_url_query();
	$encoded_query = urlencode(base64_encode(serialize($url_query)));

	$image   = '<img style="display:none" src="' . trim($options['base_url'], " \t\n\r\0\x0B/") . '/mtracking.gif?d=' . $encoded_query . '" alt="mautic is open source marketing automation" />';

	echo $image;
}

/**
 * Handle mauticform shortcode
 * example: [mauticform id="1"]
 *
 * @param  array $atts
 * @return string
 */
function wpmautic_shortcode( $atts )
{
	$options = get_option('wpmautic_options');
	$base_url = trim($options['base_url'], " \t\n\r\0\x0B/");
	$mauticform = shortcode_atts(array('id'), $atts);

	return '<script type="text/javascript" src="' . $base_url . '/form/generate.js?id=' . $atts['id'] . '"></script>';
}

function wpmautic_dwc_shortcode( $atts, $content = null )
{
	$options  = get_option('wpmautic_options');
	$base_url = trim($options['base_url'], " \t\n\r\0\x0B/");
	$atts     = shortcode_atts(array('slot' => ''), $atts, 'mauticcontent');
	$http     = new \GuzzleHttp\Client(
		array(
			'base_uri' => $base_url,
			'cookies'  => true,
			'headers'  => array(
				'HTTP_X_FORWARDED_FOR' => wpmautic_client_ip()
			)
		)
	);

	try {
		// This loads the cookies
		$http->get('/mtracking.gif');
	} catch (\GuzzleHttp\Exception\BadResponseException $e) {
		// If we don't have a mautic_session_id, we can't proceed
		return $content;
	}

	try {
		$response = $http->get('/dwc/' . $atts['slot']);
	} catch (\GuzzleHttp\Exception\BadResponseException $e) {
		// If we get a ServerException, just return original $content
		return $content;
	}

	$dwcContent = $response->getBody();

	if ($response->getStatusCode() === 200 && ! empty($dwcContent)) {
		return $dwcContent;
	}

	return $content;
}

/**
 * Builds and returns additional data for URL query
 *
 * @return array
 */
function wpmautic_get_url_query()
{
	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

	$attrs = array();
	$attrs['title']	 = wpmautic_wp_title();
	$attrs['language']  = get_locale();
	$attrs['referrer']  = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $current_url;
	$attrs['url']	   = $current_url;
	return $attrs;
}

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function wpmautic_wp_title( $title = '', $sep = '' ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= trim(wp_title($sep, false));

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

	return $title;
}

function wpmautic_client_ip() {
	if (! empty( $_SERVER['HTTP_CLIENT_IP'])) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (! empty( $_SERVER['HTTP_X_FORWARDED_FOR'])) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return apply_filters('wpb_get_ip', $ip);
}