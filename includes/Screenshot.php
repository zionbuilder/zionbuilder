<?php

namespace ZionBuilder;

use ZionBuilder\CommonJS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Install
 *
 * @package ZionBuilder
 */
class Screenshot {
	const URL_ARGUMENT = 'zionbuilder-screenshoot';

	public function __construct() {

		if ( isset( $_GET[self::URL_ARGUMENT] ) && $_GET[self::URL_ARGUMENT] === 'true' ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );
		}
	}

	public function on_enqueue_scripts() {
		CommonJS::register_scripts();
		// Load Scripts
		Plugin::instance()->scripts->enqueue_script(
			'zb-screenshot',
			'js/screenshot.js',
			[
				'zb-rest',
			],
			Plugin::instance()->get_version(),
			true
		);
	}
}
