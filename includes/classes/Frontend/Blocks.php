<?php

namespace Mautic\WP_Mautic\Frontend;

class Blocks {

  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action( 'init', array( $this, 'wpmautic_register_form_block' ) );
  }

  /**
   * Register the Mautic Form block for the editor.
   *
   * @return void
   */
  public function wpmautic_register_form_block() {
    $script_path = VPMAUTIC_PLUGIN_DIR . 'assets/js/form-block.js';
    $style_path  = VPMAUTIC_PLUGIN_DIR . 'assets/css/form-block.css';

    wp_register_style(
      'wpmautic-form-block-editor',
      plugins_url( 'assets/css/form-block.css', VPMAUTIC_PLUGIN_FILE ),
      array(),
      (string) filemtime( $style_path )
    );

    if ( ! function_exists( 'register_block_type' ) ) {
      return;
    }

    wp_register_script(
      'wpmautic-form-block',
      plugins_url( 'assets/js/form-block.js', VPMAUTIC_PLUGIN_FILE ),
      array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
      (string) filemtime( $script_path ),
      true
    );

    register_block_type(
      VPMAUTIC_PLUGIN_DIR . 'includes/blocks/form',
      array(
        'editor_script'   => 'wpmautic-form-block',
        'editor_style'    => 'wpmautic-form-block-editor',
        'render_callback' => array( $this, 'wpmautic_render_form_block' ),
      )
    );
  }

  /**
   * Render the form block.
   *
   * The editor receives an iframe preview. The public page keeps the JavaScript embed.
   *
   * @param array $attributes Block attributes.
   * @return string
   */
  public function wpmautic_render_form_block( $attributes ) {
    $form_id = isset( $attributes['formId'] ) ? absint( $attributes['formId'] ) : 0;
    $embed   = new Form_Embed();

    return $embed->render( $form_id, $this->wpmautic_is_editor_preview() );
  }

  /**
   * Server-side block rendering in the editor is a REST request.
   *
   * @return bool
   */
  private function wpmautic_is_editor_preview() {
    return defined( 'REST_REQUEST' ) && REST_REQUEST;
  }

}
