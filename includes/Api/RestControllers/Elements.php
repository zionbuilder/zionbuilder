<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Elements
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Elements extends RestApiController {

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
	protected $base = 'elements';

	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/get_element_options_form',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'get_element_options_form' ],
					'permission_callback' => [ $this, 'get_element_options_form_permissions_check' ],
					'args'                => [
						'element_data' => [
							'required' => true,
						],
					],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
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
	public function get_element_options_form_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error(
				'rest_forbidden',
				esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ),
				[ 'status' => $this->authorization_status_code() ]
			);
		}

		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_element_options_form( $request ) {
		$element_data     = $request->get_param( 'element_data' );
		$elements_manager = Plugin::$instance->elements_manager;
		$element_instance = $elements_manager->get_element_instance_with_data( $element_data );

		return rest_ensure_response(
			[
				'form' => $element_instance->dynamic_options_form(),
			]
		);
	}

}
