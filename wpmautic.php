<?php
/**
 * Plugin Name: WP Mautic
 * Plugin URI: https://github.com/mautic/mautic-wordpress
 * Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
 * Version: 0.0.1
 * Author: Mautic community
 * Author URI: http://mautic.org
 * License: GPL2
 */

add_action('admin_menu', 'wpmautic_settings');
add_action('wp_footer', 'wpmautic_function');

function wpmautic_settings() 
{
	include_once(dirname(__FILE__) . '/options.php');
	add_options_page('WP Mautic Settings', 'WPMautic', 'manage_options', 'wpmautic', 'wpmautic_options_page');
}

//
function wpmautic_function( $atts, $content = null )
{
	$options = get_option('wpmautic_options');

	$image   = '<img src="' . trim($options['base_url'], ' \t\n\r\0\x0B/') . '/mtracking.gif' . '" />';

	echo $image;
}
