<?php
/*
Plugin Name: WP OPcache
Plugin URI: http://wordpress.org/plugins/flush-opcache/
Description: This plugin allows to manage Zend OPcache inside your WordPress admin dashboard.
Author: Infogérance Linux
Version: 2.2
Text Domain: flush-opcache
Domain Path: /languages
Author URI: https://mnt-tech.fr/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


// Translation
function fo_load_textdomain() {
	load_plugin_textdomain( 'flush-opcache', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

// All actions
if ( is_multisite() && is_main_site() ) {
	add_action( "network_admin_menu", 'flush_opcache_menu' );

	// Handle options update in network mode
	add_action( 'network_admin_edit_update',  'flush_opcache_update_network_options' );
} else {
	add_action( "admin_menu", 'flush_opcache_menu' );
}
add_action( 'admin_init', 'flush_opcache' );
add_action( 'admin_bar_menu', 'flush_opcache_button', 100 );
add_action( 'plugins_loaded', 'fo_load_textdomain' );
add_action( 'admin_init', 'register_flush_opache_settings' );
add_action( 'upgrader_process_complete', 'flush_opcache_after_wp_update' );

// Add button in admin bar
function flush_opcache_button() {
	global $wp_admin_bar;

	if ( ! is_user_logged_in() || ! is_admin_bar_showing() ) {
		return false;
	}

	// User verification
	if ( ! is_admin() ) {
		return false;
	}

	// Button parameters
	$flush_url = add_query_arg( array( 'flush_opcache_action' => 'flushopcacheall' ) );
	$nonced_url = wp_nonce_url( $flush_url, 'flush_opcache_all' );

	// Admin button only on main site in MS edition or admin bar if normal edition
	if ( ( is_multisite() && is_super_admin() && is_main_site() ) || ! is_multisite() ) {
		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'flush_opcache_button',
			'title' => __( 'Flush PHP OPcache', 'flush-opcache' ),
			'meta' => array( 'title' => __( 'Flush PHP OPcache', 'flush-opcache' ) ),
			'href' => $nonced_url
			)
		);
	}
}

// Where we handle OPcache flush action
function flush_opcache() {
	if ( ! isset( $_REQUEST['flush_opcache_action'] ) ) {
		return;
	}

	// User's verification
	if ( ! is_admin() ) {
		wp_die( __( 'Sorry, you can\'t flush OPcache.', 'flush-opcache' ) );
	}

	// Show notice when flush is done  
	$action = sanitize_key( $_REQUEST['flush_opcache_action'] );
	if ( $action == 'done' ) {
		if ( is_multisite() ) {
			add_action( 'network_admin_notices', 'show_opcache_notice' );
		} else {
			add_action( 'admin_notices', 'show_opcache_notice' );
		}
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
function flush_opcache_menu() {
	if ( is_multisite() && is_super_admin() && is_main_site() ) {
		add_submenu_page( 'settings.php', __( 'WP OPcache Options', 'flush-opcache' ), 'WP OPcache', 'manage_network_options', 'flush-opcache', 'flush_opcache_options' );
	} elseif ( ! is_multisite() ) {
		add_options_page( __( 'WP OPcache Options', 'flush-opcache' ), 'WP OPcache', 'manage_options', 'flush-opcache', 'flush_opcache_options' );
	}
}

// Register settings
function register_flush_opache_settings() {
	register_setting( 'flush-opcache-settings-group', 'flush-opcache-upgrade' );
	register_setting( 'flush-opcache-settings-group', 'flush-opcache-preload' );
}

function dummy_sanitize( $options ) {
	return $options;
}

// Manage submenu page
function flush_opcache_options() {
	if ( ! is_admin() ) {
		wp_die( __( 'Sorry, you are not allowed to access this page.', 'flush-opcache' ) );
	}

	if ( ! extension_loaded( 'Zend OPcache' ) ) {
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
		<?php if ( is_multisite() ) { ?>
		<form method="post" action="edit.php?action=update">
		<?php wp_nonce_field( 'update_flush_opcache_options', 'update_flush_opcache_options' ); ?>
		<?php } else { ?>
		<form method="post" action="options.php">
		<?php } ?>
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
								<label for="flush-opcache-preload"><?php _e( 'Precompile php files each time opcache is flushed aka "OPcache Prewarm"', 'flush-opcache' ); ?></label>
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

// Handle options update in network mode only
function flush_opcache_update_network_options() {
	// Avoid CSRF, nonce is included in form with wp_nonce_field function
	check_admin_referer( 'update_flush_opcache_options', 'update_flush_opcache_options' );

	// TODO improve this shitty thing or wait until one day network options is properly handled by settings API...
	if ( isset( $_REQUEST['flush-opcache-upgrade'] ) &&  $_REQUEST['flush-opcache-upgrade'] == 1 ) {
		update_option( 'flush-opcache-upgrade', 1 );
	} else {
		update_option( 'flush-opcache-upgrade', 0 );
	}
	if ( isset( $_REQUEST['flush-opcache-preload'] ) && $_REQUEST['flush-opcache-preload'] == 1 ) {
		update_option( 'flush-opcache-preload', 1 );
	} else {
		update_option( 'flush-opcache-preload', 0 );
	}

  // At last we redirect back to our options page.
	wp_redirect( add_query_arg(
		array(
			'page'    => 'flush-opcache',
			'updated' => 'true'
		),
		network_admin_url( 'settings.php' )
		)
	);
	exit;
}

// Flush OPcache after upgrade if enable
function flush_opcache_after_wp_update() { 
	if ( get_option( 'flush-opcache-upgrade' ) === 1 ) {
		wp_opcache_reset();
	}
}

// Where OPcache is actually flushed
function wp_opcache_reset() {
	opcache_reset();
	if ( get_option( 'flush-opcache-preload' ) === 1 ) {
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
