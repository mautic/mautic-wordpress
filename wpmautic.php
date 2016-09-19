<?php
/**
 * Plugin Name: WP Mautic
 * Plugin URI: https://github.com/mautic/mautic-wordpress
 * Description: This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site
 * Version: 2.0.0
 * Author: Mautic community
 * Author URI: http://mautic.org
 * License: GPL2
 * Text Domain: mautic-wordpress
 */

// Prevent direct access to this file.
defined( 'ABSPATH' )  or die('This file should not be accessed directly!');

define( 'WPMP_ROOT_DIR', str_replace( '\\', '/', dirname(__FILE__) ) );
define( 'WPMP_ROOT_URL', rtrim( plugin_dir_url(__FILE__), '/' ) );

if( !class_exists( 'WP_Mautic' ) ){
	
	class WP_Mautic{

		/**
         * Construct the plugin object
         */
        public function __construct()
        {
			//Includes
			require_once( WPMP_ROOT_DIR .'/lib/AutoLoader.php' );
			require_once( WPMP_ROOT_DIR . '/classes/WpMauticHelper.php' );
			require_once( WPMP_ROOT_DIR . '/classes/WpMauticApi.php' );
			// Load plugin text domain
			load_textdomain('mautic-wordpress', WPMP_ROOT_DIR. '/languages/mautic-wordpress-' . get_locale() . '.mo');
			
			// Register hooks that are fired when the plugin is activated and deactivated.
			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		
			// Load plugin admin menu
			add_action( 'admin_menu', array( &$this, 'add_menu' ) );  
			add_action( 'admin_init', array( &$this, 'admin_init' ) );  
			
			// Register admin styles and scripts
			add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_styles' ) );
			//add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
			//Add tracking pixel
			if( get_option('wpmautic_tracking') == 'pixel' ){
				
				add_action('wp_footer', array( 'WP_Mautic_Helper' , 'tracking_pixel'));
			}
			if( get_option('wpmautic_tracking') == 'js' ){
				
				add_action( 'wp_footer', array( 'WP_Mautic_Helper' , 'tracking_js') );
			
			}
			//Add shortcodes
			add_shortcode( 'mauticform', array( &$this, 'wpmautic_form_shortcode' ) );
			add_shortcode('mautic', array( &$this, 'wpmautic_shortcode' ) );
		
			//Hook main actions on init hook.
			add_action( 'init',  array( &$this, 'mautic_dispatch' ), 20 );
			//Hook main actions on init hook.
			add_action( 'init',  array( &$this, 'wpmautic_save_lead_id' ), 100 );
			
        } // END public function __construct
        
        /**
        * Activate the plugin
        */
        public static function activate(){	
		
			//do nothing            
        
        } // END public static function activate
        
        /**
        * Deactivate the plugin
        */     
        public static function deactivate()
        {
           
           // Dectivation activities - clear access tokens
			$api = new Wp_Mautic_Api();
			$api->reset();
			
        } // END public static function deactivate()
        
        /**
		* hook into WP's admin_init action hook
		*/
		public function admin_init(){	
			
			// Register settings
			add_settings_section(
				'mautic_api_setting_section', //section ID
				__( 'API settings', 'mautic-wordpress'), //section title
				 array( &$this, 'mautic_settings_callback_function' ), //function to print settings output (echo)
				'wpmp-screen' //menu slug of admin page were setiings are displayed
			);

			register_setting( 'mautic_api_setting_section', 'wpmautic_url',  array( 'WP_Mautic_Helper' , 'sanitize_mautic_url' ));
			register_setting( 'mautic_api_setting_section', 'wpmautic_client_key' );
			register_setting( 'mautic_api_setting_section', 'wpmautic_client_secret' );
			register_setting( 'mautic_api_setting_section', 'wpmautic_oauth_version' );
			
			// Register settings
			add_settings_section(
				'mautic_tracking_setting_section', //section ID
				__( 'Tracking settings', 'mautic-wordpress'), //section title
				 array( &$this, 'mautic_settings_callback_function' ), //function to print settings output (echo)
				'wpmp-screen' //menu slug of admin page were setiings are displayed
			);
			
			register_setting( 'mautic_tracking_setting_section', 'wpmautic_tracking');
			register_setting( 'mautic_tracking_setting_section', 'wpmautic_tracking_users_pixel');
			register_setting( 'mautic_tracking_setting_section', 'wpmautic_tracking_only_logged_in');
			register_setting( 'mautic_tracking_setting_section', 'wpmautic_tracking_user_fields');
			register_setting( 'mautic_tracking_setting_section', 'wpmautic_tracking_user_field');
			register_setting( 'mautic_tracking_setting_section', 'wpmautic_tracking_meta_field');
			register_setting( 'mautic_tracking_setting_section', 'mautic_field_name');
			
			
			add_settings_section(
				'mautic_tokens', //section ID
				__( 'Tokens', 'mautic-wordpress'), //section title
				 array( &$this, 'mautic_settings_callback_function' ), //function to print settings output (echo)
				'wpmp-screen' //menu slug of admin page were setiings are displayed
			);
			
			register_setting( 'mautic_tokens', 'wpmautic_access_token');
			register_setting( 'mautic_tokens', 'wpmautic_access_token_secret');
			register_setting( 'mautic_tokens', 'wpmautic_access_token_expires');
			register_setting( 'mautic_tokens', 'wpmautic_refresh_token');
			
		} // END public function admin_init
		
	
		//
		public function mautic_settings_callback_function()
		{
			
		}
		
		/**
		* add a menu
		*/     
		public function add_menu()
		{
			global $menu, $submenu;
			
			add_menu_page( __('Mautic','mautic-wordpress'), __('Mautic','mautic-wordpress'), 'edit_posts', 'wpmp-screen', array( &$this, 'view_plugin_screen' ), WPMP_ROOT_URL . '/assets/img/icon_small.png', '91' );
		
		}
		
		/*Screens*/
		public function view_plugin_screen(){
			
			if( !current_user_can( 'edit_posts' ) )
			{
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'mautic-wordpress' ) );
			}

			// Render the main page template
			include( sprintf("%s/screens/settings.php", dirname( __FILE__ ) ) );
			
			
		}
		
		/*Assets*/
		public function register_admin_styles( $hook ){
			
			wp_register_style( 'wp_mautic_admin_css',  WPMP_ROOT_URL . '/assets/css/admin-styles.css', false, '0.0.1' );
			wp_enqueue_style( 'wp_mautic_admin_css' );
			
		}
		
		public function register_admin_scripts( $hook ){
			
		}	
		
		/**
		 * Handle mauticform shortcode
		 * example: [mauticform id="1"]
		 *
		 * @param  array $atts
		 * @return string
		 */
		public function wpmautic_form_shortcode( $atts, $content = null )
		{	
			
			$a = shortcode_atts( array(
			
				'id' => '',
			
			), $atts );
			
			$base_url = trim( get_option( 'wpmautic_url' ), " \t\n\r\0\x0B/" );
			echo '<script type="text/javascript" src="' . $base_url . '/form/generate.js?id=' . $a['id'] . '"></script>';
			
		}
		
		/**
		 * Handle mautic shortcode. Must include a type attribute.
		 * Allowable types are:
		 *  - form
		 *  - content
		 * example: [mautic type="form" id="1"]
		 * example: [mautic type="content" slot="slot_name"]Default Content[/mautic]
		 * example: [mautic type="video" gate-time="15" form-id="1" src="https://www.youtube.com/watch?v=QT6169rdMdk"]
		 *
		 * @param      $atts
		 * @param null $content
		 *
		 * @return string
		 */
		public function wpmautic_shortcode( $atts, $content = null )
		{
			$atts = shortcode_atts(array(
				'type' => null,
				'id' => null,
				'slot' => null,
				'src' => null,
				'width' => null,
				'height' => null,
				'form-id' => null,
				'gate-time' => null
			), $atts);

			switch ($atts['type'])
			{
				case 'form':
					return wpmautic_form_shortcode( $atts );
				case 'content':
					return wpmautic_dwc_shortcode( $atts, $content );
				case 'video':
					return wpmautic_video_shortcode( $atts );
			}

			return false;
		}
		
		public function wpmautic_video_shortcode( $atts )
		{
			$video_type = '';
			$atts = shortcode_atts(array(
				'gate-time' => 15,
				'form-id' => '',
				'src' => '',
				'width' => 640,
				'height' => 360
			), $atts);

			if (empty($atts['src']))
			{
				return __( 'You must provide a video source. Add a src="URL" attribute to your shortcode. Replace URL with the source url for your video.', 'mautic-wordpress' );
			}

			if (empty($atts['form-id']))
			{
				return __( 'You must provide a mautic form id. Add a form-id="#" attribute to your shortcode. Replace # with the id of the form you want to use.', 'mautic-wordpress' );
			}

			if (preg_match('/^.*((youtu.be)|(youtube.com))\/((v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))?\??v?=?([^#\&\?]*).*/', $atts['src']))
			{
				$video_type = 'youtube';
			}

			if (preg_match('/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/', $atts['src']))
			{
				$video_type = 'vimeo';
			}

			if (strtolower(substr($atts['src'], -3)) === 'mp4')
			{
				$video_type = 'mp4';
			}

			if (empty($video_type))
			{
				return __( 'Please use a supported video type. The supported types are youtube, vimeo, and MP4.', 'mautic-wordpress' );
			}

			return '<video height="' . $atts['height'] . '" width="' . $atts['width'] . '" data-form-id="' . $atts['form-id'] . '" data-gate-time="' . $atts['gate-time'] . '">' .
					'<source type="video/' . $video_type . '" src="' . $atts['src'] . '" /></video>';
		}
		
		public function wpmautic_dwc_shortcode( $atts, $content = null)
		{
			$base_url = trim( get_option( 'wpmautic_url' ), " \t\n\r\0\x0B/" );
			$atts     = shortcode_atts(array('slot' => ''), $atts, 'mautic');

			return '<div class="mautic-slot" data-slot-name="' . $atts['slot'] . '">' . $content . '</div>';
		}
		
		
		public function wpmautic_save_lead_id()
		{	
			if( is_user_logged_in() && !is_admin() ){
				$current_user = wp_get_current_user();
				$lead_id = get_user_meta( $current_user->ID, 'mautic_lead_id', true );
				if( !$lead_id ){//We haven't save LEAD id yet, let's try
				
					try{
						$api = new Wp_Mautic_Api();
						$ip = WP_Mautic_Helper::get_client_ip();
						$lead_api = $api->get_mautic_lead_context();
						$filter = 'email:'.$current_user->user_email;
						$leads = $lead_api->getList( $filter, 0, 1);

						if( $leads['total'] == 1 ){
							
							$lead_id = intval( $leads['leads'][0]['id'] );

							add_user_meta( $current_user->ID, 'mautic_lead_id', $lead_id, true );
							$mautic_lead = array(
								'firstname' => isset( $current_user->first_name ) ? $current_user->first_name : '',
								'lastname'	=> isset( $current_user->last_name ) ? $current_user->last_name : '',
							);

							$lead = $lead_api->edit($lead_id, $mautic_lead, true);
						
						}
						
						
					}
					catch( Exception $e ){
				
						
					}	
					
				}
			}
		}
		
		/**
		 * Process $_POST and $_GET variables
		 *
		 * 
		 * @return void
		 */
		public function mautic_dispatch(){
			
			//Create session 
			if(!session_id()) {
			
				session_start();
			
			}
			//Save recieved OAuth token
			
			if( ( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) ) || ( isset( $_GET['state'] ) && isset( $_GET['code'] ) ) ){
								
				$api = new Wp_Mautic_Api();
				$api->authorize();
				
			}
			//Only administrator in admin area can do this
			if( is_admin() && current_user_can( 'activate_plugins' ) ){
				
				if(  isset( $_POST['authorize_mautic'] ) &&  $_POST['authorize_mautic'] == '1' && check_admin_referer( 'authorize_mautic', 'mautic_nonce' ) ){
					
					$api = new Wp_Mautic_Api();
					$api->authorize( false );
					
				}
				else if(  isset( $_POST['reauthorize_mautic'] ) &&  $_POST['reauthorize_mautic'] == 1 && check_admin_referer( 'reauthorize_mautic', 'mautic_nonce' )  ){
				
					$api = new Wp_Mautic_Api();
					$api->authorize( true );
					
				}
				
				else if(  isset( $_POST['reset_mautic'] ) &&  $_POST['reset_mautic'] == 1 && check_admin_referer( 'reset_mautic' , 'mautic_nonce_reset') ){
				
					$api = new Wp_Mautic_Api();
					$api->reset();
					
				}
			}
			
			
		}
		
		
	}// END class WP_Mautic{
	
	
	
}// END if( !class_exists( 'WP_Mautic' ) ){

if( class_exists( 'WP_Mautic' ) ){
		
		// instantiate the plugin class
		$wp_mautic = new WP_Mautic();
		
} // END if( class_exists( 'WP_Mautic' ) ){
