<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\User;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class User
 *
 * @package ZionBuilder\Api\RestControllers
 */
class UserController extends RestApiController {

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
	protected $base = 'user-data';

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
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'save_item' ],
					'permission_callback' => [ $this, 'save_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Checks if a given request to save page
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function save_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to save this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * This function will save the page builder pages
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function save_item( $request ) {
		$params = $request->get_params();

		// update the page with the new changes
		$result = User::save_user_data( $params );

		if ( is_wp_error( $result ) ) {
			return new \WP_Error( 'save_error', $result->get_error_message(), [ 'status' => 500 ] );
		} elseif ( ! $result ) {
			return new \WP_Error( 'save_error', esc_html__( 'There was a problem saving the user data.', 'zionbuilder' ), [ 'status' => 500 ] );
		}

		return rest_ensure_response( User::get_user_data() );
	}
}
