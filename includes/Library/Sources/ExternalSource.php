<?php

namespace ZionBuilder\Library\Sources;

use ZionBuilder\Plugin;
use ZionBuilder\Nonces;
use ZionBuilder\Templates;

class ExternalSource extends BaseSource {
	public function on_init( $args ) {
		// Set headers
		$this->request_headers = [
			'X-WP-Nonce'   => Nonces::generate_nonce( Nonces::REST_API ),
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];
	}

	public function get_type() {
		return self::TYPE_EXTERNAL;
	}

	/**
	 * Returns a list of template items and their categories
	 *
	 * @return array
	 */
	public function get_items_and_categories() {
		return wp_remote_get( $this->url . '/connect/items-and-categories' );
	}










	public function get_items( $args = [] ) {
		$defaults = [
			'post_status'    => 'any',
			'post_type'      => Templates::TEMPLATE_POST_TYPE,
			'posts_per_page' => -1,
		];

		$args = wp_parse_args( $args, $defaults );

		$templates_list = [];

		/** @var \WP_Post[] $templates */
		$templates = Plugin::$instance->templates->get_templates();

		foreach ( $templates as $template ) {
			$template_instance = Plugin::$instance->post_manager->get_post_instance( $template->ID );
			$templates_list[]  = $template_instance->get_data_for_api();
		}

		return $templates_list;
	}

	public function get_categories() {
		return [];
	}

	public function get_item( $item_id ) {
		//return the post based on id
		$template_instance = Plugin::$instance->post_manager->get_post_instance( $item_id );

		// check if the id is valid
		if ( ! $template_instance ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return $template_instance->get_data_for_api();
	}

	public function get_item_builder_data( $item_id ) {
		// Insert using id
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $item_id );

		if ( ! $post_instance ) {
			return new \WP_Error( 'zionbuilder-error', __( 'Could not return the post instance', 'zionbuilder' ) );
		}

		$response = $post_instance->get_template_data();

		if ( ! is_array( $response ) ) {
			return new \WP_Error( 'zionbuilder-error', __( 'The builder data for this item could not be retrieved!', 'zionbuilder' ) );
		}

		return $response;
	}
}
