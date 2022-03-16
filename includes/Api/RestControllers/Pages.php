<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;

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
	protected $base = 'pages';

	/**
	 * Register routes
	 *
	 * @return void
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

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[\d]+)/get_rendered_content',
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the object.' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_rendered_content' ],
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

	/**
	 * Returns the rendered content for a given post id
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|void
	 */
	public function get_rendered_content( $request ) {
		$post_id = $request->get_param( 'id' );

		$post_instance = Plugin::$instance->post_manager->get_post_or_autosave_instance( $post_id );
		if ( ! $post_instance ) {
			return;
		}

		$post_template_data = $post_instance->get_template_data();
		Plugin::$instance->renderer->register_area( $post_id, $post_template_data );
		ob_start();
		Plugin::$instance->renderer->render_area( $post_id );
		$area_markup = ob_get_clean();

		return rest_ensure_response( $area_markup );
	}
}
