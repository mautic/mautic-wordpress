<?php

/* 
Plugin Name: WP Mautic
Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
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

	$image   = '<img src="' . $options['base_url'] . '/p/mtracking.gif' . '" />';

	echo $image;
}
