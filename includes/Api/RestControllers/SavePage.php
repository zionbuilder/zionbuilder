<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;
use ZionBuilder\CSSClasses;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class SavePage
 *
 * @package ZionBuilder\Api\RestControllers
 */
class SavePage extends RestApiController {

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
	protected $base = 'save-page';

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
		$params = $request->get_json_params();

		// update the page with the new changes
		$post_instance = Plugin::$instance->post_manager->get_post_instance( $params['page_id'] );

		if ( ! $post_instance ) {
			return;
		}

		// Set global post
		global $post;
		// phpcs:ignore
		$post = get_post( $params['page_id'], OBJECT );

		$save_post_data = $post_instance->save( $params );

		// Save css classes
		$css_classes = $request->get_param( 'css_classes' );
		if ( null !== $css_classes ) {
			// Save the css classes
			CSSClasses::save_classes( $css_classes );
		}

		do_action( 'zionbuilder/page/save', $params );

		// check for errors
		if ( ! $save_post_data ) {
			return new \WP_Error( 'save_error', esc_html__( 'There was a problem saving the page.', 'zionbuilder' ), [ 'status' => 500 ] );
		}

		$message = [ 'message' => esc_html__( 'Page successfully saved!', 'zionbuilder' ) ];

		return rest_ensure_response( $message );
	}
}
