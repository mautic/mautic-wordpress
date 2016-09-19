<?php
/*	WP Mautic
 * 	Helper class with methods for sanitization and getting data	
 * 
 * */
 
// Prevent direct access to this file.
defined( 'ABSPATH' )  or die('This file should not be accessed directly!');

if( !class_exists( 'WP_Mautic_Helper' ) ){
	
	class WP_Mautic_Helper{
		
		public static function tracking_js(){
			
			if( get_option('wpmautic_tracking_only_logged_in') ){
				
				if( ! is_user_logged_in() ){
					return;
				}
				
			}
			
			$base_url = get_option('wpmautic_url');
			
			$user_query = self::wpmautic_get_user_query();
			
			
			
			$js = '<script> 
					(function(w,d,t,u,n,a,m){w[\'MauticTrackingObject\']=n;
						w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
						m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
					})(window,document,\'script\',\''.trim($base_url, " \t\n\r\0\x0B/").'/mtc.js\',\'mt\');

					mt(\'send\', \'pageview\'';
			if( $user_query ):
				$js .= ', '.wp_json_encode($user_query);
			endif;	
			$js .=	');
				</script>';
			echo $js;	
			
		}
		
		/**
		 * Echoes Mautic tracking pixel. This method should be hooked into wp_footer.
		 *
		 * @return void
		 */
		public static function tracking_pixel(){
			
			
			if( get_option('wpmautic_tracking_only_logged_in') ){
				
				if( ! is_user_logged_in() ){
					return;
				}
				
			}
			
			$base_url = get_option('wpmautic_url');
			
			$url_query = self::wpmautic_get_url_query();
			$user_query = self::wpmautic_get_user_query();

			if ($user_query) {

				$url_query = array_merge($url_query, $user_query);

			}
			
			$encoded_query = urlencode(base64_encode(serialize($url_query)));

			$image   = '<img style="display:none" src="' . trim($base_url, " \t\n\r\0\x0B/") . '/mtracking.gif?d=' . $encoded_query . '" alt="'. __('Mautic is open source marketing automation', 'mautic-wordpress' ) . '" />';

			echo $image;
			
			return trim($base_url, " \t\n\r\0\x0B/") . '/mtracking.gif?d=' . $encoded_query;
			
		}//END public function tracking_pixel(){
			
		/**
		 * Builds and returns additional data for URL query
		 *
		 * @return array
		 */
		public static function wpmautic_get_url_query(){
			
			global $wp;

			$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

			$attrs = array(
				'title' 	=> self::wpmautic_wp_title(),
				'language' 	=> get_locale(),
				'referrer' 	=> isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $current_url,
				'url' 		=> $current_url,
			);

			return $attrs;

		}//END public function wpmautic_get_url_query(){
		
		
		/**
		 * Creates a nicely formatted and more specific title element text
		 * for output in head of document, based on current view.
		 *
		 * @param string $title Default title text for current view.
		 * @param string $sep Optional separator.
		 * @return string Filtered title.
		 */
		public static function wpmautic_wp_title( $title = '', $sep = '' ){
			
			global $paged, $page;

			if ( is_feed() )
				return $title;

			// Add the site name.
			$title .= trim(wp_title($sep, false));

			// Add a page number if necessary.
			if ( $paged >= 2 || $page >= 2 )
				$title = "$title $sep " . sprintf( __( 'Page %s', 'mautic-wordpress' ), max( $paged, $page ) );

			return $title;
		}//END public function wpmautic_wp_title( $title = '', $sep = '' ){
		
		/**
		 * Adds the user email, and other known elements about the user
		 * to the tracking pixel.
		 *
		 * @return array
		 */
		public static function wpmautic_get_user_query(){
		
			$attrs = array();

			if ( is_user_logged_in() ) {
		
				$current_user = wp_get_current_user();
				$attrs['email']	 = $current_user->user_email;
				if( get_option( 'wpmautic_tracking_users_pixel' ) ){
					
					$user_fields = get_option( 'wpmautic_tracking_user_field' );
					$meta_fields = get_option( 'wpmautic_tracking_meta_field' );
					$field_name = get_option( 'mautic_field_name' );
					
					foreach( $user_fields as $key=>$value ){
						
						if( $value == 'on' && strlen($field_name[$key]) > 0 && strlen($current_user->$key) > 0 )
							$attrs[$field_name[$key]] = $current_user->$key;	
						
					}
					
					foreach( $meta_fields as $key=>$value ){
						
						if( $value == 'on' && strlen($field_name[$key]) > 0  && strlen(get_user_meta($current_user->ID,$key,true)) > 0)
							$attrs[$field_name[$key]] = get_user_meta($current_user->ID,$key,true);	
						
					}
					
				
				}
				return $attrs;
				
			} 
		
			return null;
		
		}//END public function wpmautic_get_user_query(){
		
		/**
		 * Sanitization of Mautic instance URL
		 *	
		 * @param string $input Input string which is sanitized
		 * @return string Sanitized string
		 */
		public static function sanitize_mautic_url( $input ){
		
			$option = get_option('wpmautic_url');
			$option = esc_attr( trim( $input ) );
			return $option;
		
		}//END public function sanitize_mautic_url( $input ){
		
		
		/**
		 * Get IP address of client
		 *	
		 * @return string IP address
		 */
		public static function get_client_ip(){
			
			$ip = '';
			if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ){
				
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			
			}
			elseif( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
				
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			
			}
			elseif( !empty( $_SERVER['REMOTE_ADDR'] ) )
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			return $ip;
		
		}//END public function sanitize_mautic_url( $input ){
		
	}//END class WP_Mautic_Helper{	
}//END if( !class_exists( 'WP_Mautic_Helper' ) ){


?>
