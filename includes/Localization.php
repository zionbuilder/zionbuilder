<?php

namespace ZionBuilder;

use ZionBuilder\Whitelabel;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Localization
 * Localization class
 *
 * Will handle all interactions with the WordPress admin area and the pagebuilder
 *
 * @package ZionBuilder
 */
class Localization {
	/**
	 * Get strings
	 *
	 * @return array<string> The list of translatable strings
	 */
	public static function get_strings() {
		return apply_filters(
			'zionbuilder/localization/strings',
			[]
		);
	}
}
