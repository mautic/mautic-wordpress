<?php
/**
 * Plugin Name: WP Mautic
 * License: GPL2
 */

function wpmautic_options_page() 
{ ?>
	 <div>
		<h2>WP Mautic</h2>
		Enable Base URL for Mautic Integration.
		<form action="options.php" method="post">
			<?php settings_fields('wpmautic_options'); ?>
			<?php do_settings_sections('wpmautic'); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
	</div>
<?php
}

add_action('admin_init', 'wpmautic_admin_init');

function wpmautic_admin_init()
{
	register_setting( 'wpmautic_options', 'wpmautic_options', 'wpmautic_options_validate' );
	add_settings_section('wpmautic_main', 'Main Settings', 'wpmautic_section_text', 'wpmautic');
	add_settings_field('wpmautic_base_url', 'Base URL', 'wpmautic_base_url', 'wpmautic', 'wpmautic_main');
}

function wpmautic_section_text() 
{
}

function wpmautic_base_url() 
{
	$options = get_option('wpmautic_options');
	echo "<input id='wpmautic_base_url' name='wpmautic_options[base_url]' size='40' type='text' value='{$options['base_url']}' />";
}

function wpmautic_options_validate($input) 
{
	$options = get_option('wpmautic_options');
	$options['base_url'] = trim($input['base_url']);
	
	return $options;
}