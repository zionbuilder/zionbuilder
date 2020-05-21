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

	public static function uninstall() {
		flush_rewrite_rules();
	}
}
