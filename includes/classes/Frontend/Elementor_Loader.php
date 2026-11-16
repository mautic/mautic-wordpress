<?php

namespace Mautic\WP_Mautic\Frontend;

class Elementor_Loader {

  /**
   * Init
   *
   * @return void
   */
  public function init() {
    add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
    add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
    add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );
    add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_preview_styles' ) );
  }

  /**
   * Register the Mautic category in the Elementor panel.
   *
   * @param \Elementor\Elements_Manager $elements_manager Elements manager.
   *
   * @return void
   */
  public function register_category( $elements_manager ) {
    $elements_manager->add_category(
      'wpmautic',
      array(
        'title' => __( 'Mautic', 'wp-mautic' ),
        'icon'  => 'eicon-form-horizontal',
      )
    );
  }

  /**
   * Register Elementor widgets.
   *
   * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
   *
   * @return void
   */
  public function register_widgets( $widgets_manager ) {
    if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
      return;
    }

    $widget = new Elementor\Form_Widget();
    if ( method_exists( $widgets_manager, 'register' ) ) {
      $widgets_manager->register( $widget );
      return;
    }

    $widgets_manager->register_widget_type( $widget );
  }

  /**
   * Load editor/preview styles used by the form widget placeholder.
   *
   * @return void
   */
  public function enqueue_preview_styles() {
    if ( ! wp_style_is( 'wpmautic-form-block-editor', 'registered' ) ) {
      return;
    }

    wp_enqueue_style( 'wpmautic-form-block-editor' );
  }

}
