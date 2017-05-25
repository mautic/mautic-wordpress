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
add_action( 'wp_head', 'wpmautic_function' );
add_shortcode( 'mautic', 'wpmautic_shortcode' );
add_shortcode( 'mauticform', 'wpmautic_form_shortcode' );

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
 * Writes Tracking JS to the HTML source of WP head
 *
 * @return void
 */
function wpmautic_function() {
	$options = get_option( 'wpmautic_options' );
	$base_url = trim( $options['base_url'], " \t\n\r\0\x0B/" );
	if ( empty( $base_url ) ) {
		return;
	}

	$javascript = <<<JAVASCRIPT
(function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
    w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
    m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
})(window,document,'script','{$base_url}/mtc.js','mt');

mt('send', 'pageview');
JAVASCRIPT;

	?><script type="text/javascript"><?php echo esc_js( $javascript ); ?></script><?php
}

/**
 * Handle mautic shortcode. Must include a type attribute.
 * Allowable types are:
 *  - form
 *  - content
 * example: [mautic type="form" id="1"]
 * example: [mautic type="content" slot="slot_name"]Default Content[/mautic]
 * example: [mautic type="video" gate-time="15" form-id="1" src="https://www.youtube.com/watch?v=QT6169rdMdk"]
 *
 * @param array       $atts    Shortcode attributes.
 * @param string|null $content Default content to be displayed.
 *
 * @return string
 */
function wpmautic_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts(array(
		'type' => null,
		'id' => null,
		'slot' => null,
		'src' => null,
		'width' => null,
		'height' => null,
		'form-id' => null,
		'gate-time' => null,
	), $atts);

	switch ( $atts['type'] ) {
		case 'form':
			return wpmautic_form_shortcode( $atts );
		case 'content':
			return wpmautic_dwc_shortcode( $atts, $content );
		case 'video':
			return wpmautic_video_shortcode( $atts );
	}

	return false;
}

/**
 * Handle mauticform shortcode
 * example: [mauticform id="1"]
 *
 * @param  array $atts Shortcode attributes.
 *
 * @return string
 */
function wpmautic_form_shortcode( $atts ) {
	$options = get_option( 'wpmautic_options' );
	$base_url = trim( $options['base_url'], " \t\n\r\0\x0B/" );
	$atts = shortcode_atts( array(
		'id' => '',
	), $atts );

	if ( ! $atts['id'] ) {
		return false;
	}

	return '<script type="text/javascript" src="' . $base_url . '/form/generate.js?id=' . $atts['id'] . '"></script>';
}

/**
 * Dynamic content shortcode handling
 *
 * @param  array       $atts    Shortcode attributes.
 * @param  string|null $content Default content to be displayed.
 *
 * @return string
 */
function wpmautic_dwc_shortcode( $atts, $content = null ) {
	$options  = get_option( 'wpmautic_options' );
	$base_url = trim( $options['base_url'], " \t\n\r\0\x0B/" );
	$atts     = shortcode_atts( array(
		'slot' => '',
	), $atts, 'mautic' );

	return '<div class="mautic-slot" data-slot-name="' . $atts['slot'] . '">' . $content . '</div>';
}

/**
 * Video shortcode handling
 *
 * @param  array $atts Shortcode attributes.
 *
 * @return string
 */
function wpmautic_video_shortcode( $atts ) {
	$video_type = '';
	$atts = shortcode_atts(array(
		'gate-time' => 15,
		'form-id' => '',
		'src' => '',
		'width' => 640,
		'height' => 360,
	), $atts);

	if ( empty( $atts['src'] ) ) {
		return 'You must provide a video source. Add a src="URL" attribute to your shortcode. Replace URL with the source url for your video.';
	}

	if ( empty( $atts['form-id'] ) ) {
		return 'You must provide a mautic form id. Add a form-id="#" attribute to your shortcode. Replace # with the id of the form you want to use.';
	}

	if ( preg_match( '/^.*((youtu.be)|(youtube.com))\/((v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))?\??v?=?([^#\&\?]*).*/', $atts['src'] ) ) {
		$video_type = 'youtube';
	}

	if ( preg_match( '/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/', $atts['src'] ) ) {
		$video_type = 'vimeo';
	}

	if ( strtolower( substr( $atts['src'], -3 ) ) === 'mp4' ) {
		$video_type = 'mp4';
	}

	if ( empty( $video_type ) ) {
		return 'Please use a supported video type. The supported types are youtube, vimeo, and MP4.';
	}

	return '<video height="' . $atts['height'] . '" width="' . $atts['width'] . '" data-form-id="' . $atts['form-id'] . '" data-gate-time="' . $atts['gate-time'] . '">' .
			'<source type="video/' . $video_type . '" src="' . $atts['src'] . '" /></video>';
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
