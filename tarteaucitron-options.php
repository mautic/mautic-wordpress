<?php
/**
 * Options for Tarteaucitron
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
 * Define admin_init hook logic for tarteaucitron
 */
function wpmautic_admin_init_tarteaucitron() {
	add_settings_section(
		'wpmautic_tarteaucitron',
		__( 'GDPR with Tarteaucitron', 'wp-mautic' ),
		'wpmautic_section_text',
		'wpmautic'
	);
	add_settings_field(
		'wpmautic_tarteaucitron_orientation',
		__( 'Orientation', 'wp-mautic' ),
		'wpmautic_tarteaucitron_orientation',
		'wpmautic',
		'wpmautic_tarteaucitron'
	);
	add_settings_field(
		'wpmautic_tarteaucitron_tagmanager',
		__( 'Google Tag Manager', 'wp-mautic' ),
		'wpmautic_tarteaucitron_tagmanager',
		'wpmautic',
		'wpmautic_tarteaucitron'
	);
}

/**
 * Configure tracking with Tarteaucitron
 */
function wpmautic_tarteaucitron_orientation() {
	$orientation  = wpmautic_option( 'tarteaucitron_orientation', '' );
	$allowed_tags = array();
	?>
	<fieldset id="wpmautic_tarteaucitron_orientation">
		<label>
			<input
				type="radio"
				name="wpmautic_options[tarteaucitron_orientation]"
				value=""
				<?php
				if ( '' === $orientation ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Don\'t use GDPR cookie management with Tarteaucitron.', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="wpmautic_options[tarteaucitron_orientation]"
				value="top"
				<?php
				if ( 'top' === $orientation ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Show a banner at the top of the website.', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="wpmautic_options[tarteaucitron_orientation]"
				value="middle"
				<?php
				if ( 'middle' === $orientation ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Show a popup in the middle of the website', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
		<br/>
		<label>
			<input
				type="radio"
				name="wpmautic_options[tarteaucitron_orientation]"
				value="bottom"
				<?php
				if ( 'bottom' === $orientation ) :
					?>
					checked<?php endif; ?>
			/>
			<?php echo wp_kses( __( 'Show a banner at the bottom of the website.', 'wp-mautic' ), $allowed_tags ); ?>
		</label>
		<p>
			Tarteaucitron will load the Mautic Tracking Script (mtc.js) if the user accepts tracking cookies. Dont load it with the plugin.
		</p>
	</fieldset>
	<?php
}

/**
 * Define the Google Tag Manager Id to use
 */
function wpmautic_tarteaucitron_tagmanager() {
	$tagmanager = wpmautic_option( 'tarteaucitron_tagmanager', '' );

	?>
	<input
		id="wpmautic_tarteaucitron_tagmanager"
		name="wpmautic_options[tarteaucitron_tagmanager]"
		size="14"
		type="text"
		placeholder="GTM-......."
		value="<?php echo esc_attr( $tagmanager ); ?>"
	/>
	<?php
}


/**
 * Validate base URL input value
 *
 * @param  array $input Input data.
 * @param  array $options Array Associative array with wpmautic options.
 * @return array
 */
function wpmautic_options_validate_tarteaucitron( $input, $options ) {

	$options['tarteaucitron_orientation'] = isset( $input['tarteaucitron_orientation'] )
		? trim( $input['tarteaucitron_orientation'] )
		: '';
	if ( ! in_array( $options['tarteaucitron_orientation'], array( 'top', 'middle', 'bottom' ), true ) ) {
		$options['tarteaucitron_orientation'] = '';
	}

	$options['tarteaucitron_tagmanager'] = esc_attr( trim( $input['tarteaucitron_tagmanager'] ) );
	return $options;
}
