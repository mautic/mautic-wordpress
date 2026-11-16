<?php

namespace Mautic\WP_Mautic\Frontend\Elementor;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Mautic\WP_Mautic\Frontend\Form_Embed;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class Form_Widget extends Widget_Base {

  /**
   * Get widget name.
   *
   * @return string
   */
  public function get_name() {
    return 'wpmautic-form';
  }

  /**
   * Get widget title.
   *
   * @return string
   */
  public function get_title() {
    return __( 'Mautic Form', 'wp-mautic' );
  }

  /**
   * Get widget icon.
   *
   * @return string
   */
  public function get_icon() {
    return 'eicon-form-horizontal';
  }

  /**
   * Get widget categories.
   *
   * @return array
   */
  public function get_categories() {
    return array( 'wpmautic' );
  }

  /**
   * Get widget keywords.
   *
   * @return array
   */
  public function get_keywords() {
    return array( 'mautic', 'form', 'embed' );
  }

  /**
   * Get style dependencies.
   *
   * @return array
   */
  public function get_style_depends() {
    return array( 'wpmautic-form-block-editor' );
  }

  /**
   * Register widget controls.
   *
   * @return void
   */
  protected function register_controls() {
    $this->start_controls_section(
      'content_section',
      array(
        'label' => __( 'Mautic Form', 'wp-mautic' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
      )
    );

    $this->add_control(
      'form_id',
      array(
        'label'       => __( 'Form ID', 'wp-mautic' ),
        'type'        => Controls_Manager::NUMBER,
        'min'         => 1,
        'step'        => 1,
        'placeholder' => '1',
        'description' => __( 'Enter the form ID from Mautic. You can find it in the form URL, for example /s/forms/view/1.', 'wp-mautic' ),
      )
    );

    $this->end_controls_section();
  }

  /**
   * Render widget output on the frontend and in the editor.
   *
   * @return void
   */
  protected function render() {
    $settings = $this->get_settings_for_display();
    $form_id  = isset( $settings['form_id'] ) ? $settings['form_id'] : 0;
    $embed    = new Form_Embed();

    echo wp_kses(
      $embed->render( $form_id, $this->is_elementor_editor() ),
      $embed->allowed_html()
    );
  }

  /**
   * Whether Elementor is rendering the editor canvas.
   *
   * @return bool
   */
  private function is_elementor_editor() {
    if ( ! class_exists( '\Elementor\Plugin' ) ) {
      return false;
    }

    $elementor = Plugin::$instance;
    if ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) {
      return true;
    }

    return false;
  }

}
