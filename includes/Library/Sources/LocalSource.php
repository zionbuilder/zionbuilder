<?php

namespace ZionBuilder\Library\Sources;

use ZionBuilder\Plugin;
use ZionBuilder\Nonces;
use ZionBuilder\Templates;

class LocalSource extends BaseSource {
	public function on_init( $source_config ) {
		// Set headers
		$this->request_headers = [
			'X-WP-Nonce'   => Nonces::generate_nonce( Nonces::REST_API ),
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];
	}

	public function get_type() {
		return self::TYPE_LOCAL;
	}


	/**
	 * Returns a list of template items
	 *
	 * @return array
	 */
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
		return Plugin::$instance->templates->get_template_types();
	}


	/**
	 * Returns a single template item data for REST API
	 *
	 * @param integer $item_id The template id we want to return
	 *
	 * @return array|\WP_Error
	 */
	public function get_item( $item_id ) {
		//return the post based on id
		$template_instance = Plugin::$instance->post_manager->get_post_instance( $item_id );

		// check if the id is valid
		if ( ! $template_instance ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return $template_instance->get_data_for_api();
	}

	/**
	 * Exports an item as zip file
	 *
	 * @param integer $item_id
	 *
	 * @return array|\WP_Error
	 */
	public function export_item( $item_id ) {
		// retrieves the template data
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $item_id );

		if ( ! $post_instance ) {
			return new \WP_Error( 'export_item', __( 'Could not return the post instance', 'zionbuilder' ) );
		}

		$template_name = get_the_title( $item_id );
		$template_data = $post_instance->get_template_data();
		$template_type = get_post_meta( $item_id, Templates::TEMPLATE_TYPE_META, true );

		if ( empty( $template_name ) ) {
			$template_name = 'export';
		}

		return [
			'name'          => sanitize_file_name( $template_name ),
			'template_type' => $template_type,
			'template_data' => $template_data,
		];
	}

	/**
	 * Adds a new template to DB
	 *
	 * @param array $item_data
	 *
	 * @return \WP_Error|array The inserted template data or WP_Error in case of failure
	 */
	public function create_item( $item_data ) {
		$item_data = wp_parse_args(
			$item_data,
			[
				'title'         => __( 'Template', 'zionbuilder' ),
				'template_type' => 'template',
				'template_data' => [],
			]
		);

		$post_id = Templates::create_template(
			$item_data['title'],
			$item_data
		);

		// Check to see if the post was succesfully created
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		//return the post based on id
		$template_instance = Plugin::$instance->post_manager->get_post_instance( $post_id );

		// check if the id is valid
		if ( ! $template_instance ) {
			return new \WP_Error( 'post_not_found', __( 'Your post id could not be found!', 'zionbuilder' ) );
		}

		return $template_instance->get_data_for_api();
	}

	/**
	 * Imports a template into DB
	 *
	 * @param integer $item_id
	 *
	 * @return \WP_Error|array The inserted template data or WP_Error in case of failure
	 */
	public function insert_item( $item_id ) {
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
