<?php
/**
 * Option page definition
 *
 * @package wp-mautic
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
		<h2><?php esc_html_e( 'WP Mautic', 'wp-mautic' ); ?></h2>
		<p><?php esc_html_e( 'Add Mautic tracking capabilities to your website.', 'wp-mautic' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'wpmautic' ); ?>
			<?php do_settings_sections( 'wpmautic' ); ?>
			<?php submit_button(); ?>
		</form>
		<h3><?php esc_html_e( 'Shortcode Examples:', 'wp-mautic' ); ?></h3>
		<ul>
			<li><?php esc_html_e( 'Mautic Form Embed:', 'wp-mautic' ); ?> <code>[mautic type="form" id="1"]</code></li>
			<li><?php esc_html_e( 'Mautic Dynamic Content:', 'wp-mautic' ); ?> <code>[mautic type="content" slot="slot_name"]<?php esc_html_e( 'Default Text', 'wp-mautic' ); ?>[/mautic]</code></li>
		</ul>
		<h3><?php esc_html_e( 'Quick Links', 'wp-mautic' ); ?></h3>
		<ul>
			<li>
				<a href="https://github.com/mautic/mautic-wordpress#mautic-wordpress-plugin" target="_blank"><?php esc_html_e( 'Plugin docs', 'wp-mautic' ); ?></a>
			</li>
			<li>
				<a href="https://github.com/mautic/mautic-wordpress/issues" target="_blank"><?php esc_html_e( 'Plugin support', 'wp-mautic' ); ?></a>
			</li>
			<li>
				<a href="https://mautic.org" target="_blank"><?php esc_html_e( 'Mautic project', 'wp-mautic' ); ?></a>
			</li>
			<li>
				<a href="http://docs.mautic.org/" target="_blank"><?php esc_html_e( 'Mautic docs', 'wp-mautic' ); ?></a>
			</li>
			<li>
				<a href="https://www.mautic.org/community/" target="_blank"><?php esc_html_e( 'Mautic forum', 'wp-mautic' ); ?></a>
			</li>
		</ul>
	</div>
	<?php
}

/**
 * Define admin_init hook logic
 */
function wpmautic_admin_init() {
	register_setting( 'wpmautic', 'wpmautic_options', 'wpmautic_options_validate' );

	add_settings_section(
		'wpmautic_main',
		__( 'Main Settings', 'wp-mautic' ),
		'wpmautic_section_text',
		'wpmautic'
	);

	add_settings_field(
		'wpmautic_base_url',
		__( 'Mautic URL', 'wp-mautic' ),
		'wpmautic_base_url',
		'wpmautic',
		'wpmautic_main'
	);
	add_settings_field(
		'wpmautic_script_location',
		__( 'Tracking script location', 'wp-mautic' ),
		'wpmautic_script_location',
		'wpmautic',
		'wpmautic_main'
	);
	add_settings_field(
		'wpmautic_fallback_activated',
		__( 'Tracking image', 'wp-mautic' ),
		'wpmautic_fallback_activated',
		'wpmautic',
		'wpmautic_main'
	);
	add_settings_field(
		'wpmautic_track_logged_user',
		__( 'Logged user', 'wp-mautic' ),
		'wpmautic_track_logged_user',
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
		placeholder="https://..."
		value="<?php echo esc_url_raw( $url, array( 'http', 'https' ) ); ?>"
	/>
	<?php
}

/**
 * Define the input field for Mautic script location
 */
function wpmautic_script_location() {
	$position     = wpmautic_option( 'script_location', '' );
	$allowed_tags = array(
		'br'   => array(),
		'code' => array(),
	);

	?>
	<fieldset id="wpmautic_script_location">
		<label>
			<input
				type="radio"
				name="wpmautic_options[script_location]"
				value="header"
				<?php
				if ( 'footer' !== $position && 'disabled' !== $position ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Added in the <code>wp_head</code> action.<br/>Inserts the tracking code before the <code>&lt;head&gt;</code> tag; can be slightly slower since page load is delayed until all scripts in <code><head></code> are loaded and processed.', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="wpmautic_options[script_location]"
				value="footer"
				<?php
				if ( 'footer' === $position ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Embedded within the <code>wp_footer</code> action.<br/>Inserts the tracking code before the <code>&lt;/body&gt;</code> tag; slightly better for performance but may track less reliably if users close the page before the script has loaded.', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
		<br />
		<label>
			<input
				type="radio"
				name="wpmautic_options[script_location]"
				value="disabled"
				<?php
				if ( 'disabled' === $position ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Visitor will not be tracked when rendering the page. Use this option to comply with GDPR regulations. If the visitor accept cookies you must execute the <code>wpmautic_send()</code> JavaScript function to start tracking.', 'wp-mautic' ), $allowed_tags ); ?>
			<br/>
			<?php echo wp_kses( __( 'However when using shortcodes, a tracking cookie will be added everytime even when tracking is disabled. This is because loading a Mautic resource (javascript or image) generate that cookie.', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
	</fieldset>
	<?php
}

/**
 * Define the input field for Mautic fallback flag
 */
function wpmautic_fallback_activated() {
	$flag         = wpmautic_option( 'fallback_activated', false );
	$allowed_tags = array(
		'br'   => array(),
		'code' => array(),
	);

	?>
	<input
		id="wpmautic_fallback_activated"
		name="wpmautic_options[fallback_activated]"
		type="checkbox"
		value="1"
		<?php
		if ( true === $flag ) :
			?>
			checked<?php endif; ?>
	/>
	<label for="wpmautic_fallback_activated">
		<?php esc_html_e( 'Activate the tracking image when JavaScript is disabled.', 'wp-mautic' ); ?>
		<br/>
		<?php echo wp_kses( __( 'Be warned, that the tracking image will always generate a cookie on the user browser side. If you want to control cookies and comply to GDPR, you must use JavaScript instead.', 'wp-mautic' ), $allowed_tags ); ?>
	</label>
	<?php
}

/**
 * Define the input field for Mautic logged user tracking flag
 */
function wpmautic_track_logged_user() {
	$flag = wpmautic_option( 'track_logged_user', false );

	?>
	<input
		id="wpmautic_track_logged_user"
		name="wpmautic_options[track_logged_user]"
		type="checkbox"
		value="1"
		<?php
		if ( true === $flag ) :
			?>
			checked<?php endif; ?>
	/>
	<label for="wpmautic_track_logged_user">
		<?php esc_html_e( 'Track user information for logged-in users', 'wp-mautic' ); ?>
	</label>
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

	$input['base_url'] = isset( $input['base_url'] )
		? trim( $input['base_url'], " \t\n\r\0\x0B/" )
		: '';

	$options['base_url']        = esc_url_raw( trim( $input['base_url'], " \t\n\r\0\x0B/" ) );
	$options['script_location'] = isset( $input['script_location'] )
		? trim( $input['script_location'] )
		: 'header';
	if ( ! in_array( $options['script_location'], array( 'header', 'footer', 'disabled' ), true ) ) {
		$options['script_location'] = 'header';
	}

	$options['fallback_activated'] = isset( $input['fallback_activated'] ) && '1' === $input['fallback_activated']
		? true
		: false;
	$options['track_logged_user']  = isset( $input['track_logged_user'] ) && '1' === $input['track_logged_user']
		? true
		: false;

	return $options;
}
