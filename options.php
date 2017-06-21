<?php
/**
 * Option page definition
 *
 * @package wpmautic
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

/**
 * HTML for the Mautic option page
 */
function wpmautic_options_page() {
	?>
    <div>
    <h2>WP Mautic</h2>
    <p>Enable Base URL for Mautic Integration.</p>
    <form action="options.php" method="post">
		<?php settings_fields( 'wpmautic_options' ); ?>
		<?php do_settings_sections( 'wpmautic' ); ?>
		<?php submit_button(); ?>
    </form>
    <h3>Shortcode Examples:</h3>
    <ul>
        <li>Mautic Form Embed: <code>[mautic type="form" id="1"]</code></li>
        <li>Mautic Dynamic Content: <code>[mautic type="content" slot="slot_name"]Default Text[/mautic]</code></li>
    </ul>
    <h3>Quick Links</h3>
    <ul>
        <li>
            <a href="https://github.com/mautic/mautic-wordpress#mautic-wordpress-plugin" target="_blank">Plugin docs</a>
        </li>
        <li>
            <a href="https://github.com/mautic/mautic-wordpress/issues" target="_blank">Plugin support</a>
        </li>
        <li>
            <a href="https://mautic.org" target="_blank">Mautic project</a>
        </li>
        <li>
            <a href="http://docs.mautic.org/" target="_blank">Mautic docs</a>
        </li>
        <li>
            <a href="https://www.mautic.org/community/" target="_blank">Mautic forum</a>
        </li>
    </ul>
    </div><?php
}

/**
 * Define admin_init hook logic
 */
function wpmautic_admin_init() {
	register_setting( 'wpmautic', 'wpmautic_options', 'wpmautic_options_validate' );

	add_settings_section(
		'wpmautic_main',
		__( 'Main Settings', 'mautic-wordpress' ),
		'wpmautic_section_text',
		'wpmautic'
	);

	add_settings_field(
		'wpmautic_base_url',
		__( 'Mautic Base URL', 'mautic-wordpress' ),
		'wpmautic_base_url',
		'wpmautic',
		'wpmautic_main'
	);
	add_settings_field(
		'wpmautic_script_location',
		__( 'Tracking script location', 'mautic-wordpress' ),
		'wpmautic_script_location',
		'wpmautic',
		'wpmautic_main'
	);
	add_settings_field(
		'wpmautic_fallback_activated',
		__( 'Fallback image', 'mautic-wordpress' ),
		'wpmautic_fallback_activated',
		'wpmautic',
		'wpmautic_main'
	);
	add_settings_field(
		'wpmautic_tracking_fields',
		__( 'Select which fields should be sent to Mautic.', 'mautic-wordpress' ),
		'wpmautic_tracking_fields',
		'wpmautic',
		'wpmautic_main'
	);
}

add_action( 'admin_init', 'wpmautic_admin_init' );

/**
 * Section text
 */
function wpmautic_section_text() {
}

/**
 * Define the input field for Mautic base URL
 */
function wpmautic_base_url() {
	$url = wpmautic_option( 'base_url', '' );

	?>
    <input
            id="wpmautic_base_url"
            name="wpmautic_options[base_url]"
            size="40"
            type="text"
            placeholder="http://..."
            value="<?php echo esc_url_raw( $url, array( 'http', 'https' ) ); ?>"
    />
	<?php
}

/**
 * Define the input field for Mautic script location
 */
function wpmautic_script_location() {
	$position = wpmautic_option( 'script_location', '' );

	?>
    <fieldset id="wpmautic_script_location">
        <label><input type="radio" name="wpmautic_options[script_location]" value="header" <?php if ( 'footer' !== $position ) : ?>checked<?php endif; ?> />
			<?php esc_html_e( 'Embedded within the `wp_head` hook', 'mautic-wordpress' ); ?>
        </label>
        <br/>
        <label>
            <input type="radio" name="wpmautic_options[script_location]" value="footer" <?php if ( 'footer' === $position ) : ?>checked<?php endif; ?> />
			<?php esc_html_e( 'Embedded within the `wp_footer` hook', 'mautic-wordpress' ); ?>
        </label>
    </fieldset>
	<?php
}

/**
 * Define the input field for Mautic fallback flag
 */
function wpmautic_fallback_activated() {
	$flag = wpmautic_option( 'fallback_activated', false );
	?>
    <input id="wpmautic_fallback_activated" name="wpmautic_options[fallback_activated]" type="checkbox" value="1"
	       <?php if ( true === $flag ) : ?>checked<?php endif; ?> />
    <label for="wpmautic_fallback_activated">
		<?php esc_html_e( 'Activate it when JavaScript is disabled ?', 'mautic-wordpress' ); ?>
    </label>
	<?php
}

/**
 * Define the inputs field for Mautic tracking fields
 */
function wpmautic_tracking_fields() {

	$user_fields = wpmautic_option( 'wpmautic_tracking_user_field' );
	$meta_fields = wpmautic_option( 'wpmautic_tracking_meta_field' );
	$field_name  = wpmautic_option( 'mautic_field_name' );

	?>
    <table>
    <tr>
    <th><?php _e( 'Send', 'mautic-wordpress' ); ?></th>
    <th><?php _e( 'Field name', 'mautic-wordpress' ); ?></th>
    <th><?php _e( 'Mautic field name', 'mautic-wordpress' ); ?></th>
    <th><?php _e( 'Your field value', 'mautic-wordpress' ); ?></th>
    </tr><?php

	global $wpdb;
	$current_user = wp_get_current_user();
	$results      = $wpdb->get_results( "SHOW columns FROM $wpdb->users", ARRAY_A );
	$val          = '';
	foreach ( $results as $result ) {
		$result_field = $result['Field'];
		$checked      = '';
		if ( isset( $user_fields[ $result_field ] ) && $user_fields[ $result_field ] == 'on' ) {
			$checked = ' checked="checked" ';
		}

		if ( isset( $field_name[ $result_field ] ) ) {
			$val = $field_name[ $result_field ];
		}
		echo '<tr class="wp_users">';
		echo '<td><input name="wpmautic_options[wpmautic_tracking_user_field][' . $result_field . ']" type="checkbox" ' . $checked . '/></td><td>' . $result_field . '</td><td><input type="text" name="wpmautic_options[mautic_field_name][' . $result_field . ']" value="' . $val . '" /></td><td>' . $current_user->$result_field . '</td>';
		echo '</tr>';
	}


	$results = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}", ARRAY_A );

	foreach ( $results as $result ) {
		$meta_value = get_user_meta( $current_user->ID, $result['meta_key'], true );
		if ( ! is_array( $meta_value ) ) {
			$checked = '';
			if ( isset( $meta_fields[ $result['meta_key'] ] ) && $meta_fields[ $result['meta_key'] ] == 'on' ) {
				$checked = ' checked="checked" ';
			}
			if ( isset( $field_name[ $result['meta_key'] ] ) ) {
				$val = $field_name[ $result['meta_key'] ];
			}
			echo '<tr class="wp_usermeta">';
			echo '<td><input name="wpmautic_options[wpmautic_tracking_meta_field][' . $result['meta_key'] . ']" type="checkbox" ' . $checked . '/></td><td>' . $result['meta_key'] . '</td><td><input type="text"  name="wpmautic_options[mautic_field_name][' . $result['meta_key'] . ']" value="' . $val . '" /></td><td>' . $meta_value . '</td>';
			echo '</tr>';
		}
	}

	?></table><?php
}

/**
 * Validate base URL input value
 *
 * @param  array $input Input data.
 *
 * @return array
 */
function wpmautic_options_validate( $input ) {
	$options = get_option( 'wpmautic_options' );

	$input['base_url'] = isset( $input['base_url'] ) ? trim( $input['base_url'], " \t\n\r\0\x0B/" ) : '';

	$options['base_url']        = esc_url_raw( trim( $input['base_url'], " \t\n\r\0\x0B/" ) );
	$options['script_location'] = isset( $input['script_location'] ) ? trim( $input['script_location'] ) : 'header';
	if ( ! in_array( $options['script_location'], array( 'header', 'footer' ), true ) ) {
		$options['script_location'] = 'header';
	}

	$options['fallback_activated'] = isset( $input['fallback_activated'] ) && '1' === $input['fallback_activated'] ? true : false;

	$options['mautic_field_name']            = isset( $input['mautic_field_name'] ) ? $input['mautic_field_name'] : '';
	$options['wpmautic_tracking_user_field'] = isset( $input['wpmautic_tracking_user_field'] ) ? $input['wpmautic_tracking_user_field'] : '';
	$options['wpmautic_tracking_meta_field'] = isset( $input['wpmautic_tracking_meta_field'] ) ? $input['wpmautic_tracking_meta_field'] : '';

	return $options;
}
