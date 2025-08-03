<?php

namespace Mautic\WP_Mautic\Backend;

use Mautic\WP_Mautic\Utils\Options;

class Admin {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
		add_action('admin_menu', array($this, 'wpmautic_settings'));
    add_action('admin_init', array($this, 'wpmautic_admin_init'));
    add_filter('plugin_action_links_' . plugin_basename(VPMAUTIC_PLUGIN_FILE), array($this, 'wpmautic_plugin_actions'), 10, 2);
  }

	/**
   * Declare option page
   */
  public function wpmautic_settings() {
    add_options_page(
      __( 'WP Mautic Settings', 'wp-mautic' ),
      __( 'WPMautic', 'wp-mautic' ),
      'manage_options',
      'wpmautic',
      array($this, 'wpmautic_options_page')
    );
  }
  
  /**
   * HTML for the Mautic option page
   */
  public function wpmautic_options_page() {
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
  public function wpmautic_admin_init() {
    register_setting( 'wpmautic', 'wpmautic_options', array($this, 'wpmautic_options_validate') );

    add_settings_section(
      'wpmautic_main',
      __( 'Main Settings', 'wp-mautic' ),
      array($this, 'wpmautic_section_text'),
      'wpmautic'
    );

    add_settings_field(
      'wpmautic_base_url',
      __( 'Mautic URL', 'wp-mautic' ),
      array($this, 'wpmautic_base_url'),
      'wpmautic',
      'wpmautic_main'
    );
    add_settings_field(
      'wpmautic_script_location',
      __( 'Tracking script location', 'wp-mautic' ),
      array($this, 'wpmautic_script_location'),
      'wpmautic',
      'wpmautic_main'
    );
    add_settings_field(
      'wpmautic_fallback_activated',
      __( 'Tracking image', 'wp-mautic' ),
      array($this, 'wpmautic_fallback_activated'),
      'wpmautic',
      'wpmautic_main'
    );
    add_settings_field(
      'wpmautic_track_logged_user',
      __( 'Logged user', 'wp-mautic' ),
      array($this, 'wpmautic_track_logged_user'),
      'wpmautic',
      'wpmautic_main'
    );
  }

  /**
   * Section text
   */
  public function wpmautic_section_text() {
  }

  /**
   * Define the input field for Mautic base URL
   */
  public function wpmautic_base_url() {
    $url = Options::get( 'base_url', '' );

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
  public function wpmautic_script_location() {
    $position     = Options::get( 'script_location', '' );
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
  public function wpmautic_fallback_activated() {
    $flag         = Options::get( 'fallback_activated', false );
    $allowed_tags = array(
      'br'   => array(),
      'code' => array(),
    );

    ?>
    <select id="wpmautic_fallback_activated" name="wpmautic_options[fallback_activated]">
      <option value="0" <?php selected( $flag, '0' ); ?>><?php esc_html_e( 'Disabled', 'wp-mautic' ); ?></option>
      <option value="1" <?php selected( $flag, '1' ); ?>><?php esc_html_e( 'Only When JavaScript is Disabled', 'wp-mautic' ); ?></option>
      <option value="2" <?php selected( $flag, '2' ); ?>><?php esc_html_e( 'Replace mtc.js', 'wp-mautic' ); ?></option>
    </select>
    <br />
    <label for="wpmautic_fallback_activated">
      <?php echo wp_kses( __( 'Use "Replace mtc.js" if you want to use the tracking pixel as the default and only way to track even if JavaScript is enabled/disabled.', 'wp-mautic' ), $allowed_tags ); ?>
      <br />
      <?php echo wp_kses( __( 'Be warned, that the tracking image will always generate a cookie on the user browser side. If you want to control cookies and comply to GDPR, you must use JavaScript instead.', 'wp-mautic' ), $allowed_tags ); ?>
    </label>
    <?php
  }

  /**
   * Define the input field for Mautic logged user tracking flag
   */
  function wpmautic_track_logged_user() {
    $flag = Options::get( 'track_logged_user', false );

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
  public function wpmautic_options_validate( $input ) {
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

    if ( isset( $input['fallback_activated'] ) && in_array( $input['fallback_activated'], array( '0', '1', '2' ), true ) ) {
      $options['fallback_activated'] = sanitize_text_field($input['fallback_activated']);
    } else {
      $options['fallback_activated'] = '0'; // Default to disabled
    }

    return $options;
  }

  /**
   * Settings Link in the ``Installed Plugins`` page
   *
   * @param array $links array of plugin action links.
   *
   * @return array
   */
  public function wpmautic_plugin_actions( $links ) {
    if ( function_exists( 'admin_url' ) ) {
      $settings_link = sprintf(
        '<a href="%s">%s</a>',
        admin_url( 'options-general.php?page=wpmautic' ),
        __( 'Settings', 'wp-mautic' )
      );
      // Add the settings link before other links.
      array_unshift( $links, $settings_link );
    }
    return $links;
  }

}