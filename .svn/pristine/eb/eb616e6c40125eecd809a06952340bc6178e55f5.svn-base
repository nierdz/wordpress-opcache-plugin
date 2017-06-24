<?php
/*
Plugin Name: Flush OPcache
Plugin URI: http://wordpress.org/plugins/flush-opcache/
Description: This plugin only does one thing : Add a simple button in admin dashboard to flush PHP OPcache.
Author: Kévin MET
Version: 1.0
Author URI: https://mnt-tech.fr/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

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
		'title' => 'Flush PHP OPcache',
		'meta' => array( 'title' => 'Flush PHP OPcache' ),
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
		wp_die( 'Sorry, you can\'t flush OPcache.' );

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
		opcache_reset();
	}

	 wp_redirect( esc_url_raw( add_query_arg( array( 'flush_opcache_action' => 'done' ) ) ) );
}

function show_opcache_notice() {
	echo '<div class="updated"><p>OPcache was successfully flushed.</p></div>';
}
?>
