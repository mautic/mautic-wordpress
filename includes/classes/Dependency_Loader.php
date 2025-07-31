<?php

namespace Mautic\WP_Mautic;

class Dependency_Loader {
  
  /**
   * Init
   *
   * @return void
   */
  public function init() {
    $this->load_frontend();
    $this->load_backend();
  }
  
  /**
   * Load Frontend
   *
   * @return void
   */
  public function load_frontend() {
    $frontend_loader = new Frontend_Loader();
    $frontend_loader->init();
  }
  
  /**
   * Load Backend
   *
   * @return void
   */
  public function load_backend() {
    $backend_loader = new Backend_Loader();
    $backend_loader->init();
  }

}