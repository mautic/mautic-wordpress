<?php
/**
 * Integrate with Tarteaucitron
 * Author: Mautic community
 * Author URI: http://mautic.org
 * Text Domain: wp-mautic
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package wp-mautic
 */

function wpmautic_enqueue_tarteaucitron_scripts() {
	$wpmautic_tagmanager = wpmautic_option( 'tarteaucitron_tagmanager', '' );
	$wpmautic_orientation = wpmautic_option( 'tarteaucitron_orientation', '' );

	// Set parameters to customize the JavaScript: tarteaucitronjs.
	$wpmautic_params = array(
		'privacyUrl' => __( esc_url( get_privacy_policy_url() ), 'wpmautic' ),
		'orientation'     => $wpmautic_orientation,
		'baseUrl' => wpmautic_base_script(),
		'googletagmanagerId' => $wpmautic_tagmanager,
	);

	// Load main tarteaucitron scripts.
	wp_enqueue_script( 'tarteaucitronjs', plugins_url().'/wp-mautic/node_modules/tarteaucitronjs/tarteaucitron.js' );

	// Register the init script
	wp_register_script( 'wp_mautic_tarteaucitron', plugins_url().'/wp-mautic/public/wpmautic-tarteaucitron.js' );

	// Customize init script with php parameters.
	wp_localize_script( 'wp_mautic_tarteaucitron', 'wpmauticParams', $wpmautic_params );

	// Enqueued script.
	wp_enqueue_script( 'wp_mautic_tarteaucitron', array('tarteaucitronjs') );
}

if ( !empty(wpmautic_option( 'tarteaucitron_orientation', '' ))) {
	add_action( 'wp_enqueue_scripts', 'wpmautic_enqueue_tarteaucitron_scripts' );
}




