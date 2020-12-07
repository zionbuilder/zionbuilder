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
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * Returns all registered bulk actions
	 *
	 * @return array<string, mixed>
	 */
	public function get_bulk_actions() {
		return apply_filters(
			'zionbuilder/api/bulk_actions',
			[
				'get_image' => [ $this, 'get_image' ],
				'parse_php' => [ $this, 'parse_php' ],
			]
		);
	}

	public function get_items( $request ) {
		$actions  = $request->get_param( 'actions' );
		$post_id  = $request->get_param( 'post_id' );
		$response = [];

		$registered_actions = $this->get_bulk_actions();

		// Set main post data
		Plugin::$instance->post_manager->switch_to_post( $post_id );
		if ( is_array( $actions ) ) {
			foreach ( $actions as $action_key => $action_config ) {
				if ( array_key_exists( $action_config['type'], $registered_actions ) ) {
					$callback              = $registered_actions[$action_config['type']];
					$response[$action_key] = call_user_func( $callback, $action_config['config'] );
				}
			}
		}

		return rest_ensure_response( $response );
	}

	public function parse_php( $php_code ) {
		try {
			ob_start();
			// phpcs:ignore Squiz.PHP.Eval.Discouraged
			eval( ' ?>' . $php_code );
			return ob_get_clean();
		} catch ( \ParseError $e ) {
			return [
				'error'   => true,
				'message' => $e->getMessage(),
			];
		}
	}


	public function get_image( $image_config ) {
		return WPMedia::get_image_sizes( $image_config );
	}
}
