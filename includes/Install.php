<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Install
 *
 * @package ZionBuilder
 */
class Install {
	/**
	 * Performs various actions on Plugin activation
	 *
	 * @return void
	 */
	public static function activate() {
		flush_rewrite_rules();
		// Load default settings
		self::load_default_options();
	}

	/**
	 * Adds default plugin options to DB
	 *
	 * @return void
	 */
	public static function load_default_options() {
		if ( null === Settings::get_all_values() ) {
			Settings::restore_defaults();
		}
	}
}
