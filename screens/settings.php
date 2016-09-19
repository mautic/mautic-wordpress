<?php
/*	WP Mautic
 * 	Main settings page of plugin	
 * 
 * */
// Prevent direct access to this file.
defined( 'ABSPATH' )  or die('This file should not be accessed directly!');
?>

<h1><img src="<?php echo  WPMP_ROOT_URL . '/assets/img/logo_small.png';?>" style="margin-right:1rem;" /><?php _e( 'WP Mautic' , 'mautic-wordpress' ) ?></h1>
<h2><?php _e( 'API settings' , 'mautic-wordpress' ); ?></h2>
<form action="options.php" method="post">
	<?php 
		settings_fields('mautic_api_setting_section'); 
		$mautic_url = get_option('wpmautic_url');
		$mautic_client_key = get_option('wpmautic_client_key');
		$mautic_client_secret =	get_option('wpmautic_client_secret');
		
		$authorized = false;
		$fields_ok = strlen($mautic_url) && strlen($mautic_client_key) && strlen($mautic_client_secret);
		
	?>
	
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_url"><?php _e( 'Mautic instance URL', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<input id="wpmautic_url" name="wpmautic_url" size="52" type="text" placeholder="http://..." value="<?php esc_attr_e( $mautic_url );?>" />
			</td>		
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_client_key"><?php _e( 'Client key', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<input name="wpmautic_client_key" size="52" type="text" value="<?php esc_attr_e( $mautic_client_key );?>" />
			</td>		
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_client_secret"><?php _e( 'Client secret', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<input name="wpmautic_client_secret" size="52" type="text" value="<?php esc_attr_e( $mautic_client_secret );?>" />
			</td>		
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_oauth_version"><?php _e( 'OAuth version', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<?php $oauth_version = get_option( 'wpmautic_oauth_version' ); ?>
				<input disabled="disabled" name="wpmautic_oauth_version" type="radio" value="OAuth1a" <?php if($oauth_version == 'OAuth1a') echo " checked ";?> />OAuth 1.a
				<input name="wpmautic_oauth_version" type="radio" value="OAuth2" <?php if($oauth_version == 'OAuth2') echo " checked ";?>  style="margin-left:2rem;" />OAuth 2
			</td>		
		</tr>
		
	</table>
	<input name="Submit" type="submit" class="button button-primary" value="<?php  _e('Save Changes', 'mautic-wordpress'); ?>" />
</form>

<h2><?php _e( 'How to authorize' , 'mautic-wordpress' ); ?></h2>
<p>
	<ol>
		<li><?php _e( 'In your Mautic installation create API credentials with correct callback:' , 'mautic-wordpress' ); ?> <code><?php echo admin_url('admin.php?page=wpmp-screen');?> </code></li>
		<li><?php _e( 'Fill in Mautic instance URL, Client key and Client secret and click on "Save changes" button.' , 'mautic-wordpress' ); ?></li>
		<li><?php _e( 'Click on "Authorize" button. Log into your Mautic if required and authorize plugin.' , 'mautic-wordpress' ); ?></li>
	</ol>	
</p>

<h2><?php _e( 'API authorization controls' , 'mautic-wordpress' ); ?></h2>

	<form method="post" action="" style="display:inline-block;">
<?php 
	if( ! get_option( 'wpmautic_access_token' ) ):
		wp_nonce_field( 'authorize_mautic', 'mautic_nonce' );
?>		
		<input type="hidden" name="authorize_mautic" value="1" />
		<input name="submit_authorization" type="submit" class="button button-primary" value="<?php _e('Authorize', 'mautic-wordpress'); ?>" />
<?php
	
	else:
		wp_nonce_field( 'reauthorize_mautic', 'mautic_nonce' );
?>

		<input type="hidden" name="reauthorize_mautic" value="1" />
		<input name="submit_reauthorization" type="submit" class="button button-primary" value="<?php  _e('Reauthorize', 'mautic-wordpress'); ?>" />
	
<?php	
	endif;
?>
		
	</form>
	
<?php
	if( get_option( 'wpmautic_access_token' ) ):
?>	
	<form method="post" action="" style="display:inline-block;">
		<input type="hidden" name="reset_mautic" value="1" />
		<?php wp_nonce_field( 'reset_mautic', 'mautic_nonce_reset' ); ?>
		<input name="submit_reset" type="submit" class="button button-primary" value="<?php esc_attr_e( __('Reset', 'mautic-wordpress') ); ?>" />
		
	</form>	
<?php		
	endif;	
?>	
<h2><?php _e( 'Tracking settings' , 'mautic-wordpress' ); ?></h2>
<form action="options.php" method="post">
	<?php 
		settings_fields('mautic_tracking_setting_section'); 
		
	?>		
	<table class="form-table">
			
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_tracking"><?php _e( 'Tracking method', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<?php $enable_tracking = get_option( 'wpmautic_tracking' ); ?>
				<?php _e( 'Pixel', 'mautic-wordpress' );?>&nbsp;<input name="wpmautic_tracking" type="radio" value="pixel" <?php if($enable_tracking == 'pixel') echo " checked ";?> />
				<?php _e( 'JS', 'mautic-wordpress' );?>&nbsp;<input name="wpmautic_tracking" type="radio" value="js" <?php if($enable_tracking == 'js') echo " checked ";?> />
				<?php _e( 'None', 'mautic-wordpress' );?>&nbsp;<input name="wpmautic_tracking" type="radio" value="none" <?php if($enable_tracking == 'none') echo " checked ";?> />
				
			</td>		
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_tracking_only_logged_in"><?php _e( 'Use tracking (pixel or JS) only for logged in users.', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<?php $enable_logged_in_tracking = get_option( 'wpmautic_tracking_only_logged_in' ); ?>
				<input name="wpmautic_tracking_only_logged_in" type="checkbox" value="1" <?php if($enable_logged_in_tracking == '1') echo " checked "; if($enable_tracking == 'none') echo 'disabled="disabled"'; ?> />
				
			</td>		
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_tracking_users"><?php _e( 'Enable tracking user data with pixel or JS - requires enable public updates on user related fields in Mautic. E-mail tracking is enabled always by default.', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<?php $enable_user_tracking = get_option( 'wpmautic_tracking_users_pixel' ); ?>
				<input name="wpmautic_tracking_users_pixel" type="checkbox" value="1" <?php if($enable_user_tracking == '1') echo " checked "; if($enable_tracking == 'none') echo 'disabled="disabled"'; ?> />
				
			</td>		
		</tr>
		<tr valign="top">
			<th scope="row">
				<input name="Submit" type="submit" class="button button-primary" value="<?php  _e('Save Changes', 'mautic-wordpress'); ?>" />
			</th>
			<td>
				
			</td>		
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="wpmautic_tracking_fields"><?php _e( 'Select which fields should be sent to Mautic.', 'mautic-wordpress' );?></label>
			</th>
			<td>
				<?php
					$user_fields = get_option( 'wpmautic_tracking_user_field' );
					$meta_fields = get_option( 'wpmautic_tracking_meta_field' );
					$field_name = get_option( 'mautic_field_name' );
					
				?>
				<table>
					<tr>
						<th><?php _e( 'Send', 'mautic-wordpress' );?></th>
						<th><?php _e( 'Field name', 'mautic-wordpress' );?></th>
						<th><?php _e( 'Mautic field name', 'mautic-wordpress' );?></th>
						<th><?php _e( 'Your field value', 'mautic-wordpress' );?></th>
					</tr>	
				<?php
					
					global $wpdb;
					$current_user = wp_get_current_user();
					$results = $wpdb->get_results( "SHOW columns FROM $wpdb->users", ARRAY_A );
					$val = '';
					foreach($results as $result){
						$result_field = $result['Field'];
						$checked = '';
						if( isset($user_fields[$result_field]) && $user_fields[$result_field] == 'on' )
							$checked = ' checked="checked" ';
							
						if( isset($field_name[$result_field]) )
								$val = $field_name[$result_field];
						echo'<tr class="wp_users">';
						echo '<td><input name="wpmautic_tracking_user_field[' . $result_field . ']" type="checkbox" '.$checked.'/></td>
								<td>' . $result_field . '</td>
								<td><input type="text" name="mautic_field_name['. $result_field .']" value="'.$val.'" /></td>
								<td>'.$current_user->$result_field.'</td>
								';
						echo '</tr>';
					}
					
					
					$results = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}", ARRAY_A );
				
					foreach($results as $result){
						$meta_value = get_user_meta($current_user->ID, $result['meta_key'], true);
						if( !is_array($meta_value) ){
							$checked = '';
							if( isset($meta_fields[$result['meta_key']]) && $meta_fields[$result['meta_key']] == 'on' )
								$checked = ' checked="checked" ';
							if( isset($field_name[$result['meta_key']]) )
									$val = $field_name[$result['meta_key']];	
							echo'<tr class="wp_usermeta">';
							echo '<td><input name="wpmautic_tracking_meta_field[' . $result['meta_key'] . ']" type="checkbox" '.$checked.'/></td>
									<td>' . $result['meta_key'] . '</td>
										<td><input type="text"  name="mautic_field_name['. $result['meta_key'] .']" value="'.$val.'" /></td>
										<td>'.$meta_value.'</td>
										';
							echo '</tr>';
						}
					}

				?>
				</table>
			</td>		
		</tr>	
			
	 </table>
	<input name="Submit" type="submit" class="button button-primary" value="<?php  _e('Save Changes', 'mautic-wordpress'); ?>" />
</form>	


<h2><?php _e( 'How to embed Mautic Form' , 'mautic-wordpress' ); ?></h2>
<p><?php _e( 'Shortcode example for Mautic Form Embed:', 'mautic-wordpress' );?> <code>[mautic type="form" id="1"]</code>
<p><?php _e( 'Shortcode example for Dynamic Content:', 'mautic-wordpress' );?> <code>[mautic type="content" slot="slot_name"]<?php  _e('Default content to display in case of error or unknown contact.', 'mautic-wordpress'); ?>[/mautic]</code>
<p><?php _e( 'Shortcode example for Mautic Gated Videos:', 'mautic-wordpress' );?> <code>[mautic type="video" gate-time="#" form-id="#" src="URL"]</code>
</p>
<h3><?php _e( 'Quick Links' , 'mautic-wordpress' ); ?></h3>
<ul>
	<li>
		<a href="https://mautic.org" target="_blank"><?php _e( 'Mautic project' , 'mautic-wordpress' ); ?></a>
	</li>
	<li>
		<a href="http://docs.mautic.org/" target="_blank"><?php _e( 'Mautic docs' , 'mautic-wordpress' ); ?></a>
	</li>
	<li>
		<a href="https://www.mautic.org/community/" target="_blank"><?php _e( 'Mautic forum' , 'mautic-wordpress' ); ?></a>
	</li>
</ul>
