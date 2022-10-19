<?php

namespace ZionBuilder;

use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * CSS classes
 *
 * @package ZionBuilder
 */
class CustomCode {
	public function __construct() {
		// Add custom css
		add_filter( 'zionbuilder/cache/dynamic_css', [ $this, 'add_custom_css' ] );

		// Add header scripts
		add_action( 'wp_head', [ $this, 'add_head_scripts' ], 999 );

		// Add body scripts
		add_action( 'wp_body_open', [ $this, 'add_body_scripts' ], 999 );

		// Add footer scripts
		add_action( 'wp_footer', [ $this, 'add_footer_scripts' ], 999 );
	}


	/**
	 * Returns a saved value from DB
	 *
	 * @param string $setting_key The key for the value to be returned
	 * @param mixed $default The value that needs to be returned if no existing value is saved
	 *
	 * @since 2.6.0
	 *
	 * @return mixed
	 */
	public function get_value( $setting_key, $default = null ) {
		// We need to clear the cache since the settings are updated
		Settings::clear_cache();
		$saved_values = Settings::get_value( 'custom_code', [] );

		if ( isset( $saved_values[$setting_key] ) ) {
			return $saved_values[$setting_key];
		}

		return $default;
	}


	/**
	 * Adds custom css to the dynamic css file
	 *
	 * @param string $css
	 *
	 * @return string
	 */
	public function add_custom_css( $css ) {
		$css .= $this->get_value( 'custom_css', '' );
		return $css;
	}

	public function add_head_scripts() {
		// phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->get_value( 'header_scripts', '' );
	}

	public function add_body_scripts() {
		// phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->get_value( 'body_scripts', '' );
	}

	public function add_footer_scripts() {
		// phpcs:ignore WordPress.Security.EscapeOutput
		echo $this->get_value( 'footer_scripts', '' );
	}
}
