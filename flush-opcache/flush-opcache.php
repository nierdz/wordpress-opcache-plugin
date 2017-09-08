<?php
/*
Plugin Name: WP OPcache
Plugin URI: http://wordpress.org/plugins/flush-opcache/
Description: This plugin allows to manage Zend OPcache inside your WordPress admin dashboard.
Author: Infogérance Linux
Version: 2.1
Text Domain: flush-opcache
Domain Path: /languages
Author URI: https://mnt-tech.fr/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


// Translation
add_action( 'plugins_loaded', 'fo_load_textdomain' );
function fo_load_textdomain() {
	load_plugin_textdomain( 'flush-opcache', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

// Add button in admin area
function flush_opcache_button() {
	global $wp_admin_bar;

	if ( !is_user_logged_in() || !is_admin_bar_showing() )
		return false;

	// User's verification
	if ( !current_user_can( 'activate_plugins' ))
		return false;

	// Button parameters
	$flush_url = add_query_arg( array( 'flush_opcache_action' => 'flushopcacheall' ) );
	$nonced_url = wp_nonce_url( $flush_url, 'flush_opcache_all' );
	$wp_admin_bar->add_menu( array(
		'parent' => '',
		'id' => 'flush_opcache_button',
		'title' => __( 'Flush PHP OPcache', 'flush-opcache' ),
		'meta' => array( 'title' => __( 'Flush PHP OPcache', 'flush-opcache' ) ),
		'href' => $nonced_url
		)
	);
}
add_action( 'admin_bar_menu', 'flush_opcache_button', 100 );
add_action( 'admin_init', 'flush_opcache');

// Where OPcache is actually flushed
function flush_opcache() {

	if ( !isset( $_REQUEST['flush_opcache_action'] ) )
		return;

	// User's verification
	if ( !current_user_can( 'activate_plugins' ) )
		wp_die( __( 'Sorry, you can\'t flush OPcache.', 'flush-opcache' ) );

	// Show notice when flush is done  
	$action = $_REQUEST['flush_opcache_action'];
	if ( $action == 'done' ) {
		add_action( 'admin_notices', 'show_opcache_notice' );
		return;
	}

	// Check for nonce and admin
	check_admin_referer( 'flush_opcache_all' );

	// OPcache reset
	if ( $action == 'flushopcacheall' ) {
		wp_opcache_reset();
	}

	 wp_redirect( esc_url_raw( add_query_arg( array( 'flush_opcache_action' => 'done' ) ) ) );
}

function show_opcache_notice() {
	echo '<div class="updated"><p>' . __( 'OPcache was successfully flushed.', 'flush-opcache' ) . '</p></div>';
}

// Add submenu page and register settings
add_action( 'admin_menu', 'flush_opcache_menu' );
function flush_opcache_menu() {
	add_options_page( __( 'WP OPcache Options', 'flush-opcache' ), 'WP OPcache', 'manage_options', 'flush-opcache', 'flush_opcache_options' );
	add_action( 'admin_init', 'register_flush_opache_settings' );
}

// Register settings
function register_flush_opache_settings() {
	register_setting( 'flush-opcache-settings-group', 'flush-opcache-upgrade' );
	register_setting( 'flush-opcache-settings-group', 'flush-opcache-preload' );
	register_setting( 'flush-opcache-settings-group', 'flush-opcache-help' );
}

// Manage submenu page
function flush_opcache_options() {
	if ( !current_user_can( 'activate_plugins' ) )
		wp_die( __( 'Sorry, you are not allowed to access this page.', 'flush-opcache' ) );

	if ( !extension_loaded( 'Zend OPcache' ) ) {
		echo '<div class="notice notice-error"><p>' . __( 'You do not have the Zend OPcache extension loaded, you need to install it to use this plugin.', 'flush-opcache' ) . '</p></div>';
	}

	if ( ! opcache_get_status() ) {
		echo '<div class="notice notice-error"><p>' . __( 'Zend OPcache is loaded but not activated. You need to set opcache.enable=1 in your php.ini', 'flush-opcache' ) . '</p></div>';
	}

	echo '<div class="wrap">';
	$tab = isset($_GET['tab']) ? sanitize_key( $_GET['tab'] ) : 'general';
	if ($tab == 'general') {
		manage_tabs();
		?>
		<form method="post" action="options.php">
		<?php settings_fields( 'flush-opcache-settings-group' ); ?>
		<?php do_settings_sections( 'flush-opcache-settings-group' ); ?>
			<div class="postbox">
				<h3 class="hndle" style="padding:8px 12px;">
				<span><?php _e( 'General Options', 'flush-opcache' ); ?></span>
				</h3>
				<div class="inside">
					<table class="form-table">
						<tr valign="top">
							<td>
								<input type="checkbox" name="flush-opcache-upgrade" value="1" <?php checked( 1, get_option( 'flush-opcache-upgrade' ), true ); ?> /> 
								<label for="flush-opcache-upgrade"><?php _e( 'Automatically flush OPcache after an upgrade', 'flush-opcache' ); ?></label>
							</td>
						</tr>
						<tr valign="top">
							<td>
								<input type="checkbox" name="flush-opcache-preload" value="1" <?php checked( 1, get_option( 'flush-opcache-preload' ), true ); ?> /> 
								<label for="flush-opcache-help"><?php _e( 'Precompile php files each time opcache is flushed aka "OPcache Prewarm"', 'flush-opcache' ); ?></label>
							</td>
						</tr>
						<tr valign="top">
							<td>
								<input type="checkbox" name="flush-opcache-help" value="1" <?php checked( 1, get_option( 'flush-opcache-help' ), true ); ?> /> 
								<label for="flush-opcache-help"><?php _e( 'Help us to be more famous, it\'ll add a link to this plugin in your footer', 'flush-opcache' ); ?></label>
							</td>
						</tr>
					</table>
				</div>
			</div>
		<?php
		submit_button();
	}
	if ($tab == 'statistics') {
		manage_tabs();
		require_once( "opcache.php" );
	}
	echo '</div>';
}

// Manage tabs inside submenu page
function manage_tabs() {

	// Settings tabs
	$settings_tabs = array();
	$settings_tabs['general'] = __( 'General Settings', 'flush-opcache' );
	$settings_tabs['statistics'] = __( 'Statistics', 'flush-opcache' );

	$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general_settings';
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $settings_tabs as $tab_key => $tab_caption ) {
		$active = ( $current_tab == $tab_key ) ? ' nav-tab-active' : '';
		echo '<a class="nav-tab' . $active . '" href="?page=flush-opcache&tab=' . $tab_key . '">' . $tab_caption . '</a>';
	}
	echo '</h2>';
}

// Add link in footer if enable
add_action( 'wp_footer', 'add_link_footer' ); 
function add_link_footer() { 
	if ( get_option( 'flush-opcache-help' ) ) {
		echo '<a href="https://wordpress.org/plugins/flush-opcache/">' . __( 'Powered by Flush OPcache', 'flush-opcache' ) . '</a>';
	}
}

// Flush OPcache after upgrade if enable
add_action( 'upgrader_process_complete', 'after_wp_update' );
function after_wp_update() { 
	if ( get_option( 'flush-opcache-upgrade' ) ) {
		wp_opcache_reset();
	}
}

// Where OPcache is actually flushed
function wp_opcache_reset() {
	opcache_reset();
	if ( get_option( 'flush-opcache-upgrade' ) ) {
		wp_opcache_preload();
	}
}

// Where we preload all php file
function wp_opcache_preload() {
	$di = new RecursiveDirectoryIterator( ABSPATH, RecursiveDirectoryIterator::SKIP_DOTS );
	$it = new RecursiveIteratorIterator( $di );

	foreach( $it as $file ) {
		if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
			opcache_compile_file( $file ); 
		}
	}
}

