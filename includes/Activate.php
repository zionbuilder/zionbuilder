<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Activate
 *
 * @package ZionBuilder
 */
class Activate {
	/**
	 * @throws \Exception
	 */
	public static function activate() {
		// Load default settings
		self::load_default_options();
	}

	/**
	 *
	 */
	public static function load_default_options() {
		if ( null === Settings::get_all_values() ) {
			Settings::restore_defaults();
		}
	}
}
