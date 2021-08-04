<?php

namespace ZionBuilder;

use ZionBuilder\Whitelabel;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Settings
 *
 * @package ZionBuilder
 */
class Shortcodes {
	public function __construct() {
		// Register shortcodes
		add_shortcode( 'zionbuilder', [ $this, 'print_shortcode' ] );
	}

	public function print_shortcode( $atts, $content = null ) {
		if ( ! isset( $atts['id'] ) ) {
			return __( 'Template id missing', 'zionbuilder' );
		}

		$post_instance = Plugin::$instance->post_manager->get_post_instance( $atts['id'] );

		if ( ! $post_instance ) {
			return __( 'Template not found', 'zionbuilder' );
		}

		if ( ! $post_instance->is_built_with_zion() ) {
			return sprintf(
				/* translators: %s is the whitelabel plugin name */
				__( 'Template was not built with %s', 'zionbuilder' ),
				Whitelabel::get_title()
			);
		}

		// Register the elements
		$post_template_data = $post_instance->get_template_data();
		Plugin::$instance->renderer->register_area( $atts['id'], $post_template_data );

		return Plugin::$instance->renderer->get_content( $atts['id'] );
	}
}
