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

    // Register by name. Passing a directory is WP 5.8+ and is treated as a
    // block name on 5.0-5.7, which still have register_block_type().
    register_block_type(
      'wp-mautic/form',
      array(
        'title'           => __( 'Mautic Form', 'wp-mautic' ),
        'category'        => 'embed',
        'description'     => __( 'Insert a Mautic form and preview it in the editor.', 'wp-mautic' ),
        'keywords'        => array( 'mautic', 'form' ),
        'supports'        => array(
          'html'   => false,
          'anchor' => false,
        ),
        'editor_script'   => 'wpmautic-form-block',
        'editor_style'    => 'wpmautic-form-block-editor',
        'render_callback' => array( $this, 'wpmautic_render_form_block' ),
        'attributes'      => array(
          'formId' => array(
            'type'    => 'string',
            'default' => '',
          ),
        ),
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
