<?php
/**
 * Internationalization class file
 *
 * @package flush-opcache
 */

/**
 * Internationalization class
 *
 * @package flush-opcache
 */
class Flush_Opcache_I18n {

	/**
	 * Load .mo file according to selected language
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'flush-opcache',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

}
