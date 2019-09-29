<?php

class Flush_Opcache_i18n {

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'flush-opcache',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

}
