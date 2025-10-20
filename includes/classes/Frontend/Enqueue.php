<?php

namespace Mautic\WP_Mautic\Frontend;

use Mautic\WP_Mautic\Utils\Options;

class Enqueue {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->enqueue();
  }
  
  /**
   * Enqueue scripts and styles
   *
   * @return void
   */
  public function enqueue() {
    $this->enqueue_scripts();
  }
  
  /**
   * Enqueue scripts
   *
   * @return void
   */
  public function enqueue_scripts() {
    $script_location = Options::get( 'script_location' );
    
    if ( 'header' === $script_location ) {
      add_action( 'wp_head', array( $this, 'wpmautic_inject_script' ) );
    } else {
      add_action( 'wp_footer', array( $this, 'wpmautic_inject_script' ) );
    }

    if ( 'disabled' !== $script_location && true === Options::get( 'fallback_activated', false ) ) {
      add_action( 'wp_footer', array( $this, 'wpmautic_inject_noscript' ) );
    }
  }

  /**
   * Writes Tracking JS to the HTML source
   *
   * @return void
   */
  public function wpmautic_inject_script() {
    // Load the Mautic tracking library mtc.js if it is not disabled.
    $base_url        = $this->wpmautic_base_script();
    $script_location = Options::get( 'script_location' );
    $attrs           = $this->wpmautic_get_tracking_attributes();
    ?>
    <script type="text/javascript" >
      function wpmautic_send(){
        if ('undefined' === typeof mt) {
          if (console !== undefined) {
            console.warn('WPMautic: mt not defined. Did you load mtc.js ?');
          }
          return false;
        }
        // Add the mt('send', 'pageview') script with optional tracking attributes.
        mt('send', 'pageview'<?php echo count( $attrs ) > 0 ? ', ' . wp_json_encode( $attrs ) : ''; ?>);
      }

    <?php
    // Mautic is not configured, or user disabled automatic tracking on page load (GDPR).
    if ( ! empty( $base_url ) && 'disabled' !== $script_location ) :
      ?>
      (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
        w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
        m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
      })(window,document,'script','<?php echo esc_url( $base_url ); ?>','mt');

      wpmautic_send();
      <?php
    endif;
    ?>
    </script>
    <?php
  }

  /**
   * Writes Tracking image fallback to the HTML source
   * This is a separated function because <noscript> tags are not allowed in header !
   *
   * @return void
   */
  public function wpmautic_inject_noscript() {
    $base_url = Options::get( 'base_url', '' );
    if ( empty( $base_url ) ) {
      return;
    }

    $url_query = $this->wpmautic_get_url_query();
    $payload   = rawurlencode( base64_encode( serialize( $url_query ) ) );
    ?>
    <noscript>
      <img src="<?php echo esc_url( $base_url ); ?>/mtracking.gif?d=<?php echo esc_attr( $payload ); ?>" style="display:none;" alt="<?php echo esc_attr__( 'Mautic Tags', 'wp-mautic' ); ?>" />
    </noscript>
    <?php
  }

  /**
   * Generate the mautic script URL to be used outside of the plugin when
   * necessary
   *
   * @return string
   */
  private function wpmautic_base_script() {
    $base_url = Options::get( 'base_url', '' );
    if ( empty( $base_url ) ) {
      return;
    }

    return $base_url . '/mtc.js';
  }

  /**
   * Builds and returns additional data for URL query
   *
   * @return array
   */
  private function wpmautic_get_url_query() {
    global $wp;
    $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

    $attrs = $this->wpmautic_get_tracking_attributes();

    $attrs['language']   = get_locale();
    $attrs['page_url']   = $current_url;
    $attrs['page_title'] = function_exists( 'wp_get_document_title' )
      ? wp_get_document_title()
      : wp_title( '&raquo;', false );
    $attrs['referrer']   = function_exists( 'wp_get_raw_referer' )
      ? wp_get_raw_referer()
      : null;
    if ( false === $attrs['referrer'] ) {
      $attrs['referrer'] = $current_url;
    }

    return $attrs;
  }

  /**
   * Create custom query parameters to be injected inside tracking
   *
   * @return array
   */
  private function wpmautic_get_tracking_attributes() {
    $attrs = $this->wpmautic_get_user_query();

    /**
     * Update / add data to be sent within Mautic tracker
     *
     * Default data only contains the 'language' key but every added key to the
     * array will be sent to Mautic.
     *
     * @since 2.1.0
     *
     * @param array $attrs Attributes to be filters, default ['language' => get_locale()]
     */
    return apply_filters( 'wpmautic_tracking_attributes', $attrs );
  }

  /**
   * Extract logged user information to be sent within Mautic tracker
   *
   * @return array
   */
  private function wpmautic_get_user_query() {
    $attrs = array();

    if (
      true === Options::get( 'track_logged_user', false ) &&
      is_user_logged_in()
    ) {
      $current_user       = wp_get_current_user();
      $attrs['email']     = $current_user->user_email;
      $attrs['firstname'] = $current_user->user_firstname;
      $attrs['lastname']  = $current_user->user_lastname;

      // Following Mautic fields has to be created manually and the fields must match these names.
      $attrs['wp_user']              = $current_user->user_login;
      $attrs['wp_alias']             = $current_user->display_name;
      $attrs['wp_registration_date'] = date(
        'Y-m-d',
        strtotime( $current_user->user_registered )
      );
    }

    return $attrs;
  }
  
}