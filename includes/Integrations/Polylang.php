<?php

namespace ZionBuilder\Integrations;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Gutenberg
 *
 * @package ZionBuilder\Integrations
 */
class Polylang implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'polylang';
	}


	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return defined( 'POLYLANG_VERSION' );
	}


	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_filter( 'zionbuilderpro/theme/template_post_id', [ $this, 'change_post_id' ], 10 );
	}

	/**
	 * Sets the proper post id for polylang translated pages
	 *
	 * @param string $post_id The preview content
	 *
	 * @return string The preview content
	 */
	public function change_post_id( $post_id ) {
		if ( function_exists( 'pll_get_post' ) ) {
			$post_id = \pll_get_post( $post_id ) ?: $post_id;
		}

		return $post_id;
	}
}
