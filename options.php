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
	add_settings_section( 'wpmautic_main', 'Main Settings', 'wpmautic_section_text', 'wpmautic' );
	add_settings_field( 'wpmautic_base_url', 'Mautic Base URL', 'wpmautic_base_url', 'wpmautic', 'wpmautic_main' );
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
	?>
	<input id="wpmautic_base_url" name="wpmautic_options[base_url]" size="40" type="text" placeholder="http://..." value="<?php echo esc_url_raw( $options['base_url'], array( 'http', 'https' ) ); ?>" />
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

	return $options;
}
