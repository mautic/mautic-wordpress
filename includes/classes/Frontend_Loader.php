<?php

namespace Mautic\WP_Mautic;

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

}