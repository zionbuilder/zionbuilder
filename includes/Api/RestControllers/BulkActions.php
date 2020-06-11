<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;
use ZionBuilder\WPMedia;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class BulkActions extends RestApiController {

	/**
	 * Api endpoint namespace
	 *
	 * @var string
	 */
	protected $namespace = 'zionbuilder/v1';

	/**
	 * Api endpoint
	 *
	 * @var string
	 */
	protected $base = 'bulk-actions';


	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				'args'   => [
					'actions' => [
						'description' => __( 'The list of actions we need to perform.', 'zionbuilder' ),
						'type'        => 'object',
					],
					'post_id' => [
						'description' => __( 'The post id for which we need to retrieve data.' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Checks if a given request has access to read posts.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}


	public function get_items( $request ) {
		$actions  = $request->get_param( 'actions' );
		$post_id  = $request->get_param( 'post_id' );
		$response = [];

		// Set main post data
		Plugin::$instance->post_manager->switch_to_post( $post_id );
		if ( is_array( $actions ) ) {
			foreach ( $actions as $action_key => $action_config ) {
				switch ( $action_config['type'] ) {
					case 'get_image':
						$response[$action_key] = $this->get_image( $action_config['config'] );
						break;

					default:
						# code...
						break;
				}
			}
		}

		return rest_ensure_response( $response );
	}


	private function get_image( $image_config ) {
		return WPMedia::get_image_sizes( $image_config );
	}
}
