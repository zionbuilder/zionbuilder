<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Media
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Media extends RestApiController {

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
	protected $base = 'media';

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
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'get_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
			]
		);
	}

	/**
	 * Checks if a given request has access to read a data set.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$images    = $request->get_param( 'images' );
		$image_ids = [];

		foreach ( $images as $image ) {
			$image_id = attachment_url_to_postid( $image );
			if ( $image_id ) {
				$image_ids[$image] = $image_id;
			}
		}

		return rest_ensure_response( $image_ids );
	}
}
