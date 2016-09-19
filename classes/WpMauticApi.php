<?php
/*	WP Mautic
 * 	Main API class
 * 
 * */
 
// Prevent direct access to this file.
defined( 'ABSPATH' )  or die('This file should not be accessed directly!');

if( !class_exists( 'WP_Mautic_Api' ) ){
	
	class WP_Mautic_Api{
		
		
		//API params
		private $params;
		
		/**
		 * Constructor initialize necessary variables
		 *
		 * @return void
		 */
		public function __construct()
		{
				
			$this->params = $this->initialize_params();	
			
		}
		/**
		 * Initialize OAuth parameters
		 *
		 * @return array
		 */
		private function initialize_params(){
			
			$params = array(
				
				'baseUrl' 		=> $this->getMauticBaseUrl(),
				'version'		=> get_option('wpmautic_oauth_version'),
				'clientKey'		=> get_option('wpmautic_client_key'),
				'clientSecret' 	=> get_option('wpmautic_client_secret'),
				'callback' 		=> admin_url( 'admin.php?page=wpmp-screen' ),
				
			);
			
			$params['accessToken'] = get_option( 'wpmautic_access_token' );
			$params['accessTokenSecret'] = get_option( 'wpmautic_access_token_secret' );
			$params['accessTokenExpires'] = get_option( 'wpmautic_access_token_expires' );
			$params['refreshToken'] = get_option( 'wpmautic_refresh_token' );
				
			return $params;
			
		}
		
		/**
		 * Initiate Auth object
		 *
		 * @return  string
		 */
		public function getMauticAuth( $clear_access_token = false )
		{
			$settings = $this->params;
			
			if( $clear_access_token )
			{
				
				unset($settings['accessToken']);
				unset($settings['accessTokenSecret']);
				unset($settings['accessTokenExpires']);
				unset($settings['refreshToken']);
				unset($_SESSION['OAuth1a']);
				unset($_SESSION['OAuth2']);
				unset($_SESSION['oauth']);
				
			}
			
			$auth = \Mautic\Auth\ApiAuth::initiate($settings);
			try{
				//Try to reauth only with refresh token
				if( strlen($settings['refreshToken']) && strlen($settings['clientKey']) && strlen($settings['clientSecret']) && strlen($settings['baseUrl']) && strlen($settings['version']) ){
					if ( $auth->validateAccessToken() ){
						
						if( $auth->accessTokenUpdated() ){
								
								$accessTokenData = $auth->getAccessTokenData();
								
								update_option( 'wpmautic_access_token', $accessTokenData['access_token'] );
								update_option( 'wpmautic_access_token_expires', $accessTokenData['expires'] );
								update_option( 'wpmautic_refresh_token', $accessTokenData['refresh_token'] );
								update_option( 'wpmautic_access_token_secret', $accessTokenData['access_token_secret'] );
								
						}
							
					}
				}
			}
			catch(Exception $e){
				
				
				error_log( $e->getMessage()  );
			}
			
			return $auth;
		}
		
		/**
		 * Create sanitized Mautic Base URL without the slash at the end.
		 *
		 * @return string
		 */
		public function getMauticBaseUrl()
		{
			return trim( get_option('wpmautic_url'), ' \t\n\r\0\x0B/' );
		}
		
		/*	
		 * Use OAuth autorization to authorize plugin API calls
		 * 
		 * */
		public function authorize( $clear_access_token = false ){
			
			//Initialize auth object
			$auth = $this->getMauticAuth( $clear_access_token ); 
			
			try{
				
				if ( $auth->validateAccessToken() ){
						
					if( $auth->accessTokenUpdated()){
						
						$accessTokenData = $auth->getAccessTokenData();
						
						update_option( 'wpmautic_access_token', $accessTokenData['access_token'] );
						update_option( 'wpmautic_access_token_expires', $accessTokenData['expires'] );
						update_option( 'wpmautic_refresh_token', $accessTokenData['refresh_token'] );
						update_option( 'wpmautic_access_token_secret', $accessTokenData['access_token_secret'] );
						
					}
					
				}
				
			}
			catch(Exception $e){
				
				
				error_log( $e->getMessage()  );
			}
			
			wp_safe_redirect( admin_url('admin.php?page=wpmp-screen') );
			
			exit;
			
		}
		
		/**
		 * Reset authorization tokens
		 *
		 * @return void
		 */
		public function reset(){
			
			update_option( 'wpmautic_access_token', '' );
			update_option( 'wpmautic_access_token_secret', '' );
			update_option( 'wpmautic_access_token_expires', '' );
			update_option( 'wpmautic_refresh_token', '' );
			
		}
		
		/**
		 * Create object for Mautic Lead API
		 *
		 * @return Lead context object
		 */
		public function get_mautic_lead_context(){
			
			$auth = $this->getMauticAuth( false );
			$ip = WP_Mautic_Helper::get_client_ip();
			$mautic_url = $this->getMauticBaseUrl();
			$lead_api = \Mautic\MauticApi::getContext("leads", $auth, $mautic_url . '/api/');
			return $lead_api;
			
		}
	
		
	}// END class WP_Mautic_Api{
	
	
}// END if( !class_exists( 'WP_Mautic_Api' ) ){
