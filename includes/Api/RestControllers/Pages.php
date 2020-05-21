<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;
use ZionBuilder\Post\BasePostType;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Pages
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Pages extends RestApiController {
	protected $namespace = '/zionbuilder/v1';
	protected $base      = 'pages';

	/**
	 * Register routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[\d]+)/lock',
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the object.' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'lock_item' ],
					'permission_callback' => [ $this, 'lock_item_permissions_check' ],
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
	 * @return true|\WP_Error true if the request has read access, WP_Error object otherwise
	 */
	public function lock_item_permissions_check( $request ) {
		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function lock_item( $request ) {
		if ( ! function_exists( 'wp_set_post_lock' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$post_locked = wp_set_post_lock( $request->get_param( 'id' ) );
		if ( ! $post_locked ) {
			return new \WP_Error(
				'rest_invalid_post_or_user',
				__( 'Could not lock post for current user.', 'zionbuilder' ),
				[ 'status' => 500 ]
			);
		}

		return rest_ensure_response(
			[
				'message' => __( 'Post locked.', 'zionbuilder' ),
			]
		);
	}
}
