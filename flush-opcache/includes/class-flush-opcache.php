<?php

class Flush_Opcache {

  protected $name;
  protected $version;

  public function __construct() {
    $this->version = FLUSH_OPCACHE_VERSION;
    $this->name = FLUSH_OPCACHE_NAME;
    $this->load_dependencies();
    $this->set_locale();
    $this->create_admin();
  }

  private function load_dependencies() {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flush-opcache-i18n.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-flush-opcache-admin.php';
  }

  private function set_locale() {
    $plugin_i18n = new Flush_Opcache_i18n();
    add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
  }

  private function create_admin() {
    $admin = new Flush_Opcache_Admin( $this->get_name(), $this->get_version() );
    if ( is_multisite() && is_main_site() ) {
      add_action( "network_admin_menu", array( $admin, 'flush_opcache_admin_menu' ) );
      add_action( 'network_admin_edit_flush_opcache_update',  array( $admin, 'flush_opcache_update_network_options' ) );
    } else {
      add_action( "admin_menu", array( $admin, 'flush_opcache_admin_menu' ) );
    }
    add_action( 'admin_init', array( $admin, 'flush_opcache' ) );
    add_action( 'admin_bar_menu', array( $admin, 'flush_opcache_button' ), 100 );
    add_action( 'admin_init', array( $admin, 'register_flush_opache_settings' ) );
    add_action( 'upgrader_process_complete', array( $admin, 'flush_opcache_after_wp_update' ) );
    if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'flush-opcache' ) ) {
      add_action( 'admin_enqueue_scripts', array( $admin, 'enqueue_style' ) );
    }
    if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'flush-opcache-statistics' ) ) {
      add_action( 'admin_enqueue_scripts', array( $admin, 'enqueue_script' ) );
    }
  }

  public function get_name() {
    return $this->name;
  }

  public function get_version() {
    return $this->version;
  }

}
