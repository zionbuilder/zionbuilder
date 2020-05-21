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
class TwentyNineteen implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'twenty_nineteen';
	}


	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		$theme = wp_get_theme();
		return ( 'twentynineteen' === $theme->get_stylesheet() || ( $theme->parent() ? 'twentynineteen' === $theme->parent()->get_stylesheet() : false ) );
	}


	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_filter( 'zionbuilder/preview/content', [ $this, 'preview_content_filter' ] );
	}

	/**
	 * Adds an empty div with 'site-branding' css class in order to prevent console errors
	 *
	 * @param string $content The preview content
	 *
	 * @return string The preview content
	 */
	public function preview_content_filter( $content ) {
		$content .= '<div class="site-branding"></div>';

		return $content;
	}
}
