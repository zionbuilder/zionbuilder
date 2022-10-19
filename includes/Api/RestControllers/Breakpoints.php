<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Responsive;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Assets
 *
 * @since 3.4.0
 * @package ZionBuilder\Api\RestControllers
 */
class Breakpoints extends RestApiController {

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
	protected $base = 'breakpoints';

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
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			[
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Checks if a given request has access to regenerate cache
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
	 * This function will delete the Zion Builder cache from cache folder
	 *
	 * @param \WP_REST_Request $request
	 * @since 3.4.0
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( $request ) {
		return rest_ensure_response( Responsive::get_breakpoints() );
	}

	/**
	 * Saves the breakpoints to DB
	 *
	 * @param \WP_REST_Request $request
	 * @since 3.4.0
	 *
	 * @return \WP_REST_Response
	 */
	public function update_item( $request ) {
		Responsive::save_breakpoints( $request->get_params() );

		return rest_ensure_response( Responsive::get_breakpoints() );
	}
}
