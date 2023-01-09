<?php
/**
 * Main admin class file
 *
 * @package flush-opcache
 */

/**
 * Flush OPCache through command line.
 *
 * @package flush-opcache
 */
class Flush_Opcache_Cli {

	/**
	 * Name of the plugin
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Version of the plugin
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
	 * Returns plugin version.
	 */
	public function plugin_version() {
		WP_CLI::line( 'Ver: ' . $this->version );
	}

	/**
	 * Returns plugin name.
	 */
	public function plugin_name() {
		WP_CLI::line( 'Name: ' . $this->name );
	}

	/**
	 * Try to flush OPCache.
	 */
	public function flush() {
		try {
			$admin = new Flush_Opcache_Admin( $this->name, $this->version );
			$admin->flush_opcache_reset();
			WP_CLI::success( 'OPcache was successfully flushed.' );
		} catch ( \Throwable $e ) {
			WP_CLI::error( sprintf( 'Unable to query OPcache status: %s.', $e->getMessage() ), $e->getCode() );
		}
	}
}
