<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Uninstall
 * @package ZionBuilder
 */
class Uninstall {

	/**
	 * Performs various actions when the plugin is disabled
	 *
	 * @return void
	 */
	public static function uninstall() {
		flush_rewrite_rules();
	}
}
