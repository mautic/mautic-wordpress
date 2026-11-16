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
   * Gutenberg ServerSideRender uses WP_REST_Block_Renderer_Controller.
   *
   * That controller is registered at /wp/v2/block-renderer/{name} since
   * WordPress 5.0. REST_REQUEST is also true for public post rendering
   * (/wp/v2/pages, /wp/v2/posts), which must keep the JavaScript embed.
   *
   * @return bool
   */
  private function wpmautic_is_editor_preview() {
    $route = $this->wpmautic_get_rest_route();

    return '' !== $route && 0 === strpos( $route, '/wp/v2/block-renderer/' );
  }

  /**
   * REST route from the query var registered by rest_api_init().
   *
   * rest_api_loaded() reads $GLOBALS['wp']->query_vars['rest_route'] during
   * parse_request, before WP_Query is populated, so get_query_var() is empty.
   *
   * @return string
   */
  private function wpmautic_get_rest_route() {
    if ( ! isset( $GLOBALS['wp'] ) || ! is_object( $GLOBALS['wp'] ) ) {
      return '';
    }

    if ( empty( $GLOBALS['wp']->query_vars['rest_route'] ) || ! is_string( $GLOBALS['wp']->query_vars['rest_route'] ) ) {
      return '';
    }

    return '/' . ltrim( untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] ), '/' );
  }

}
