<?php

namespace Mautic\WP_Mautic;

use Mautic\WP_Mautic\Backend\Admin;

class Backend_Loader {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->load_admin();
  }
  
  /**
   * Load Admin
   *
   * @return void
   */
  public function load_admin() {
    $admin = new Admin();
    $admin->init();
  }

}