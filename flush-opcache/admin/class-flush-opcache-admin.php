<?php
/**
 * Main admin class file
 *
 * @package flush-opcache
 */

/**
 * Main class
 *
 * Handle all stuff in admin area
 *
 * @package flush-opcache
 */
class Flush_Opcache_Admin {

	/**
	 * Name of the plugin
	 *
	 * @var string
	 */
	private $name;

	/**
	 * * Version of the plugin
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Construct of the main class
	 *
	 * Only here to set name and version
	 *
	 * @param string $name Name of the plugin.
	 * @param string $version Version of the plugin.
	 */
	public function __construct( $name, $version ) {
		$this->name    = $name;
		$this->version = $version;
	}

	/**
	 * Enqueue d3js in admin area
	 */
	public function enqueue_script() {
		wp_enqueue_script(
			'd3js',
			plugin_dir_url( __FILE__ ) . 'js/d3.min.js',
			array(),
			'3.5.17',
			false
		);
	}

	/**
	 * Generate menu pages in admin area
	 */
	public function flush_opcache_admin_menu() {
		if ( is_multisite() && is_super_admin() && is_main_site() ) {
			add_menu_page(
				__( 'WP OPcache Settings', 'flush-opcache' ),
				__( 'WP OPcache', 'flush-opcache' ),
				'manage_network_options',
				'flush-opcache',
				array( $this, 'flush_opcache_admin_options' )
			);
			add_submenu_page(
				'flush-opcache',
				__( 'WP OPcache Settings', 'flush-opcache' ),
				__( 'Settings', 'wporg' ),
				'manage_network_options',
				'flush-opcache',
				array( $this, 'flush_opcache_admin_options' )
			);
			add_submenu_page(
				'flush-opcache',
				__( 'WP OPcache Statistics', 'flush-opcache' ),
				__( 'Statistics', 'flush-opcache' ),
				'manage_network_options',
				'flush-opcache-statistics',
				array( $this, 'flush_opcache_admin_stats' )
			);
		} elseif ( ! is_multisite() && is_admin() ) {
			add_menu_page(
				__( 'WP OPcache Settings', 'flush-opcache' ),
				__( 'WP OPcache', 'flush-opcache' ),
				'manage_options',
				'flush-opcache',
				array( $this, 'flush_opcache_admin_options' )
			);
			add_submenu_page(
				'flush-opcache',
				__( 'WP OPcache Settings', 'flush-opcache' ),
				__( 'Settings', 'wporg' ),
				'manage_options',
				'flush-opcache',
				array( $this, 'flush_opcache_admin_options' )
			);
			add_submenu_page(
				'flush-opcache',
				__( 'WP OPcache Statistics', 'flush-opcache' ),
				__( 'Statistics', 'flush-opcache' ),
				'manage_options',
				'flush-opcache-statistics',
				array( $this, 'flush_opcache_admin_stats' )
			);
		}
	}

	/**
	 * Populate setup admin page
	 */
	public function flush_opcache_admin_options() {
		if ( ! is_admin() ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'wporg' ) );
		}
		if ( ! extension_loaded( 'Zend OPcache' ) ) {
			echo '<div class="notice notice-error">
              <p>' . esc_html__( 'You do not have the Zend OPcache extension loaded, you need to install it to use this plugin.', 'flush-opcache' ) . '</p>
            </div>';
		}
		if ( ! opcache_get_status() ) {
			echo '<div class="notice notice-error">
              <p>' . esc_html__( 'Zend OPcache is loaded but not activated. You need to set opcache.enable=1 in your php.ini', 'flush-opcache' ) . '</p>
            </div>';
		} ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Settings', 'flush-opcache' ); ?></h1>
		<?php if ( isset( $_GET['page'] ) && isset( $_GET['settings-updated'] ) && 'flush-opcache' === $_GET['page'] && 'true' === $_GET['settings-updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification ?>
		<div id="message" class="updated notice is-dismissible">
			<p><?php esc_html_e( 'Settings saved.', 'wporg' ); ?></p>
		</div>
			<?php
		}
		if ( is_multisite() ) {
			?>
		<form method="post" action="edit.php?action=flush_opcache_update">
			<?php
			wp_nonce_field( 'update_flush_opcache_options', 'update_flush_opcache_options' );
		} else {
			?>
		<form method="post" action="options.php">
			<?php
		}
		settings_fields( 'flush-opcache-settings-group' );
		do_settings_sections( 'flush-opcache-settings-group' );
		?>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Automatically flush OPcache after an upgrade', 'flush-opcache' ); ?>
				</th>
				<td>
				<input
					type="checkbox"
					name="flush-opcache-upgrade"
					value="1"
					<?php checked( 1, get_site_option( 'flush-opcache-upgrade' ), true ); ?>
				>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Hide Flush PHP Opcache button in admin bar', 'flush-opcache' ); ?>
				</th>
				<td>
				<input
					type="checkbox"
					name="flush-opcache-hide-button"
					value="1"
					<?php checked( 1, get_site_option( 'flush-opcache-hide-button' ), true ); ?>
				>
				</td>
			</tr>
			</tbody>
		</table>
			<?php submit_button(); ?>
		</form>
		<?php
		$base_url   = remove_query_arg( 'settings-updated' );
		$flush_url  = add_query_arg( array( 'flush_opcache_action' => 'flushopcacheall' ), $base_url );
		$nonced_url = wp_nonce_url( $flush_url, 'flush_opcache_all' );
		?>
		<form method="post" action="<?php echo esc_url( $nonced_url ); ?>">
			<p class="submit">
				<input
					style="color: #FFF; background: #DD3D36; border-color: #DD3D36; text-shadow: unset; box-shadow: unset;"
					type="submit"
					name="submit"
					id="submit"
					class="button button-primary"
					value="<?php esc_html_e( 'Flush PHP OPcache', 'flush-opcache' ); ?>">
			</p>
		</form>
		<?php
	}

	/**
	 * Populate statistics admin page
	 */
	public function flush_opcache_admin_stats() {
		require_once 'opcache.php';
	}

	/**
	 * Register settings group to use settings API
	 */
	public function register_flush_opcache_settings() {
		register_setting( 'flush-opcache-settings-group', 'flush-opcache-upgrade' );
		register_setting( 'flush-opcache-settings-group', 'flush-opcache-hide-button' );
	}

	/**
	 * Generate flush button in admin bar
	 */
	public function flush_opcache_button() {
		global $wp_admin_bar;
		if ( ! is_user_logged_in() || ! is_admin_bar_showing() ) {
			return false;
		}
		if ( ! is_admin() ) {
			return false;
		}
		if ( get_site_option( 'flush-opcache-hide-button' ) === '1' ) {
			return false;
		}
		$base_url   = remove_query_arg( 'settings-updated' );
		$flush_url  = add_query_arg( array( 'flush_opcache_action' => 'flushopcacheall' ), $base_url );
		$nonced_url = wp_nonce_url( $flush_url, 'flush_opcache_all' );
		if ( ( is_multisite() && is_super_admin() && is_main_site() ) || ( ! is_multisite() && is_admin() ) ) {
			$wp_admin_bar->add_menu(
				array(
					'parent' => '',
					'id'     => 'flush_opcache_button',
					'title'  => __( 'Flush PHP OPcache', 'flush-opcache' ),
					'meta'   => array( 'title' => __( 'Flush PHP OPcache', 'flush-opcache' ) ),
					'href'   => $nonced_url,
				)
			);
		}
	}

	/**
	 * Check if we need to flush OPcache
	 */
	public function flush_opcache() {
		if ( ! isset( $_REQUEST['flush_opcache_action'] ) ) {
			return;
		}
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			return;
		}
		if ( ! is_admin() ) {
			wp_die( esc_html__( 'Sorry, you can\'t flush OPcache.', 'flush-opcache' ) );
		}
		$action = sanitize_key( $_REQUEST['flush_opcache_action'] );
		if ( 'done' === $action ) {
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'show_opcache_notice' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'show_opcache_notice' ) );
			}
			return;
		}
		check_admin_referer( 'flush_opcache_all' );
		if ( 'flushopcacheall' === $action ) {
			$this->flush_opcache_reset();
		}
		wp_safe_redirect( esc_url_raw( add_query_arg( array( 'flush_opcache_action' => 'done' ) ) ) );
		exit;
	}

	/**
	 * Where OPcache is actually flushed
	 */
	public function flush_opcache_reset() {
		if ( function_exists( 'opcache_reset' ) ) {
			if ( ini_get( 'opcache.file_cache' ) && is_writable( ini_get( 'opcache.file_cache' ) ) ) {
				$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( ini_get( 'opcache.file_cache' ), RecursiveDirectoryIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
				foreach ( $files as $fileinfo ) {
					$todo = ( $fileinfo->isDir() ? 'rmdir' : 'unlink' );
					$todo( $fileinfo->getRealPath() );
				}
			}
			opcache_reset();
		}
	}

	/**
	 * Display a notice when OPcache was flushed
	 */
	public function show_opcache_notice() {
		?>
	<div id="message" class="updated notice is-dismissible">
		<p><?php esc_html_e( 'OPcache was successfully flushed.', 'flush-opcache' ); ?></p>
	</div>
		<?php
	}

	/**
	 * Because settings API does not work in multisite we have to do it ourself
	 */
	public function flush_opcache_update_network_options() {
		check_admin_referer( 'update_flush_opcache_options', 'update_flush_opcache_options' );
		if ( isset( $_REQUEST['flush-opcache-upgrade'] ) && '1' === $_REQUEST['flush-opcache-upgrade'] ) {
			update_site_option( 'flush-opcache-upgrade', 1 );
		} else {
			update_site_option( 'flush-opcache-upgrade', 0 );
		}
		if ( isset( $_REQUEST['flush-opcache-hide-button'] ) && '1' === $_REQUEST['flush-opcache-hide-button'] ) {
			update_site_option( 'flush-opcache-hide-button', 1 );
		} else {
			update_site_option( 'flush-opcache-hide-button', 0 );
		}
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'             => 'flush-opcache',
					'settings-updated' => 'true',
				),
				network_admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Check if we need to flush OPcache after an update
	 */
	public function flush_opcache_after_wp_update() {
		if ( get_site_option( 'flush-opcache-upgrade' ) === 1 ) {
			$this->flush_opcache_reset();
		}
	}

}
