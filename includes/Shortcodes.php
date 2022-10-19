<?php

namespace ZionBuilder;

use ZionBuilder\Whitelabel;
use ZionBuilder\Plugin;
use ZionBuilder\Assets;

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
		// Register shortcode
		add_shortcode( 'zionbuilder', [ $this, 'print_shortcode' ] );
	}


	/**
	 * Will print the zion builder template shortcode
	 *
	 * @param array $attrs
	 * @param string $content
	 *
	 * @return string
	 */
	public function print_shortcode( $attrs, $content = null ) {
		if ( ! isset( $attrs['id'] ) ) {
			return __( 'Template id missing', 'zionbuilder' );
		}

		$post_instance = Plugin::$instance->post_manager->get_post_instance( $attrs['id'] );

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
		$post_id            = $attrs['id'];
		Plugin::$instance->renderer->register_area( $attrs['id'], $post_template_data );

		$content = Plugin::$instance->renderer->get_content( $post_id );
		Assets::enqueue_assets_for_post( $post_id );
		Assets::enqueue_scripts_for_elements( $post_template_data );

		return $content;
	}
}
