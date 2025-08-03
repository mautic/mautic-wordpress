<?php

namespace Mautic\WP_Mautic\Utils;

use InvalidArgumentException;

class Options {

  /**
   * Retrieve one of the wpmautic options but sanitized
   *
   * @param  string $option  Option name to be retrieved (base_url, script_location).
   * @param  mixed  $default Default option value return if not exists.
   *
   * @return string
   *
   * @throws InvalidArgumentException Thrown when the option name is not given.
   */
  public static function get( $option, $default = null ) {
    $options = get_option( 'wpmautic_options' );

    switch ( $option ) {
      case 'script_location':
        return ! isset( $options[ $option ] ) ? 'header' : $options[ $option ];
      case 'fallback_activated':
        return isset( $options[ $option ] ) ? (bool) $options[ $option ] : true;
      case 'track_logged_user':
        return isset( $options[ $option ] ) ? (bool) $options[ $option ] : false;
      default:
        if ( ! isset( $options[ $option ] ) ) {
          if ( isset( $default ) ) {
            return $default;
          }

          throw new InvalidArgumentException( __('You must give a valid option name !', 'wp-mautic') );
        }

        return $options[ $option ];
    }
  }

}