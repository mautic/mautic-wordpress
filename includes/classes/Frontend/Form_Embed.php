<?php

namespace Mautic\WP_Mautic\Frontend;

use Mautic\WP_Mautic\Utils\Options;

class Form_Embed {

  /**
   * Render a Mautic form embed or editor preview.
   *
   * @param int|string $form_id Form ID.
   * @param bool       $preview Whether to render the editor preview.
   *
   * @return string
   */
  public function render( $form_id, $preview = false ) {
    $form_id = absint( $form_id );

    if ( ! $form_id ) {
      return $preview ? $this->render_setup_placeholder() : '';
    }

    $base_url = Options::get( 'base_url', '' );
    if ( '' === $base_url ) {
      if ( $preview ) {
        return '<p class="wpmautic-form-block__notice">' . esc_html__( 'Add your Mautic URL in the WP Mautic settings before previewing a form.', 'wp-mautic' ) . '</p>';
      }

      return '';
    }

    if ( $preview ) {
      return sprintf(
        '<iframe class="wpmautic-form-preview" src="%1$s" title="%2$s"></iframe>',
        esc_url( $base_url . '/form/' . $form_id ),
        esc_attr__( 'Mautic form preview', 'wp-mautic' )
      );
    }

    $shortcodes = new Shortcodes();
    $html       = $shortcodes->wpmautic_form_shortcode(
      array(
        'id' => (string) $form_id,
      )
    );

    return $html ? $html : '';
  }

  /**
   * Placeholder shown when no form ID is set in an editor.
   *
   * @return string
   */
  public function render_setup_placeholder() {
    return '<div class="wpmautic-form-block__setup">'
      . '<p class="wpmautic-form-block__title">' . esc_html__( 'Mautic Form', 'wp-mautic' ) . '</p>'
      . '<p class="wpmautic-form-block__help">' . esc_html__( 'Enter a form ID to insert the form and preview it here.', 'wp-mautic' ) . '</p>'
      . '</div>';
  }

  /**
   * Allowed HTML for already-escaped embed markup.
   *
   * @return array
   */
  public function allowed_html() {
    return array(
      'div'    => array(
        'class' => true,
      ),
      'p'      => array(
        'class' => true,
      ),
      'iframe' => array(
        'class'  => true,
        'src'    => true,
        'title'  => true,
        'width'  => true,
        'height' => true,
      ),
      'script' => array(
        'type' => true,
        'src'  => true,
      ),
    );
  }
}
