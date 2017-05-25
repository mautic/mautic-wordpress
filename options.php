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
	</div>
	<?php
}

/**
 * Define admin_init hook logic
 */
function wpmautic_admin_init() {
	register_setting( 'wpmautic_options', 'wpmautic_options', 'wpmautic_options_validate' );

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
	$options = get_option( 'wpmautic_options' );
	$url = isset( $options['base_url'] )?$options['base_url']:'';
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
	$options = get_option( 'wpmautic_options' );
	$position = $options['script_location'];

	?>
	<fieldset id="wpmautic_script_location">
		<label>
			<input
				type="radio"
				name="wpmautic_options[script_location]"
				value="header"
				<?php if ( $position !== 'footer' ) : ?>checked<?php endif; ?>
			/>
			<?php _e( 'Embedded within the <code>wp_head</code> hook', 'mautic-wordpress' ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="wpmautic_options[script_location]"
				value="footer"
				<?php if ( $position === 'footer' ) : ?>checked<?php endif; ?>
			/>
			<?php _e( 'Embedded within the <code>wp_footer</code> hook', 'mautic-wordpress' ); ?>
		</label>
	</fieldset>
	<?php
}

/**
 * Validate base URL input value
 *
 * @param  array $input Input data.
 * @return array
 */
function wpmautic_options_validate( $input ) {
	$options = get_option( 'wpmautic_options' );
	$options['base_url'] = trim( $input['base_url'] );
	$options['script_location'] = trim( $input['script_location'] );
	if ( ! in_array( $options['script_location'], array( 'header', 'footer' ) ) ) {
		$options['script_location'] = 'header';
	}

	return $options;
}
