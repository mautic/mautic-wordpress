<?php

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly
}

function wpmautic_options_page()
{ ?>
	 <div>
		<h2><?php _e( 'WP Mautic', 'mautic-wordpress' ); ?></h2>
		<p><?php _e( 'Enable Base URL for Mautic Integration.', 'mautic-wordpress' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields('wpmautic_options'); ?>
			<?php do_settings_sections('wpmautic'); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
		<p><?php _e( 'Shortcode example for Mautic Form Embed:', 'mautic-wordpress' ); ?> <code>[mauticform id="1"]</code></p>
		<h3><?php _e( 'Quick Links', 'mautic-wordpress' ); ?></h3>
		<ul>
			<li>
				<a href="https://github.com/mautic/mautic-wordpress#mautic-wordpress-plugin" target="_blank"><?php _e( 'Plugin docs', 'mautic-wordpress' ); ?></a>
			</li>
			<li>
				<a href="https://github.com/mautic/mautic-wordpress/issues" target="_blank"><?php _e( 'Plugin support', 'mautic-wordpress' ); ?></a>
			</li>
			<li>
				<a href="https://mautic.org" target="_blank"><?php _e( 'Mautic project', 'mautic-wordpress' ); ?></a>
			</li>
			<li>
				<a href="http://docs.mautic.org/" target="_blank"><?php _e( 'Mautic docs', 'mautic-wordpress' ); ?></a>
			</li>
			<li>
				<a href="https://www.mautic.org/community/" target="_blank"><?php _e( 'Mautic forum', 'mautic-wordpress' ); ?></a>
			</li>
		</ul>
	</div>
<?php
}

add_action('admin_init', 'wpmautic_admin_init');

function wpmautic_admin_init()
{
	register_setting( 'wpmautic_options', 'wpmautic_options', 'wpmautic_options_validate' );
	add_settings_section('wpmautic_main', 'Main Settings', 'wpmautic_section_text', 'wpmautic');
	add_settings_field('wpmautic_base_url', 'Mautic Base URL', 'wpmautic_base_url', 'wpmautic', 'wpmautic_main');
}

function wpmautic_section_text()
{
}

function wpmautic_base_url()
{
	$options = get_option('wpmautic_options');
	echo "<input id='wpmautic_base_url' name='wpmautic_options[base_url]' size='40' type='text' placeholder='http://...' value='{$options['base_url']}' />";
}

function wpmautic_options_validate($input)
{
	$options = get_option('wpmautic_options');
	$options['base_url'] = trim($input['base_url']);

	return $options;
}
