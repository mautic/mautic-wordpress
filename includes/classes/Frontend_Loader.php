<?php

namespace Mautic\WP_Mautic;

use Mautic\WP_Mautic\Frontend\Blocks;
use Mautic\WP_Mautic\Frontend\Elementor_Loader;
use Mautic\WP_Mautic\Frontend\Enqueue;
use Mautic\WP_Mautic\Frontend\Shortcodes;

class Frontend_Loader {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->load_enqueue();
    $this->load_shortcodes();
    $this->load_blocks();
    $this->load_elementor();
  }
  
  /**
   * Enqueue
   *
   * @return void
   */
  public function load_enqueue() {
    $enqueue = new Enqueue();
    $enqueue->init();
  }
  
  /**
   * Load Shortcodes
   *
   * @return void
   */
  public function load_shortcodes() {
    $shortcodes = new Shortcodes();
    $shortcodes->init();
  }

  /**
   * Load blocks
   *
   * @return void
   */
  public function load_blocks() {
    $blocks = new Blocks();
    $blocks->init();
  }

  /**
   * Load Elementor widgets
   *
   * @return void
   */
  public function load_elementor() {
    $elementor = new Elementor_Loader();
    $elementor->init();
  }

}
