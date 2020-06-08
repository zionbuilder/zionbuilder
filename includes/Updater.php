<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Updater
 *
 * @package ZionBuilder
 */
class Updater {
	public static function check_for_update() {
		$saved_version = get_option( 'zionbuilder_version' );
		$saved_version = ! empty( $saved_version ) ? $saved_version : '1.0.0';
		$new_version   = Plugin::instance()->get_version();
	}
}
