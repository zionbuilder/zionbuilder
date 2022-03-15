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
	 * @return true|\WP_Error
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
				'get_image'                => [ $this, 'get_image' ],
				'parse_php'                => [ $this, 'parse_php' ],
				'render_element'           => [ $this, 'render_element' ],
				'get_input_select_options' => [ $this, 'get_input_select_options' ],
				'search_posts'             => [ $this, 'search_posts' ],
			]
		);
	}

	public function get_items( $request ) {
		$actions  = $request->get_param( 'actions' );
		$post_id  = apply_filters( 'zionbuilder/rest/bulk_actions/post_id', $request->get_param( 'post_id' ), $request );
		$response = [];

		do_action( 'zionbuilder/rest/bulk_actions/before_get_items', $request );

		$registered_actions = $this->get_bulk_actions();

		// Set main post data
		Plugin::$instance->post_manager->switch_to_post( $post_id );

		// Set the main query
		query_posts( // phpcs:ignore WordPress.WP.DiscouragedFunctions
			[
				'p'         => $post_id,
				'post_type' => 'any',
			]
		);

		if ( is_array( $actions ) ) {
			foreach ( $actions as $action_key => $action_config ) {
				if ( array_key_exists( $action_config['type'], $registered_actions ) ) {
					do_action( 'zionbuilder/rest/bulk_actions/before_action', $action_config, $request );

					$callback              = $registered_actions[$action_config['type']];
					$response[$action_key] = call_user_func( $callback, $action_config['config'], $request );

					do_action( 'zionbuilder/rest/bulk_actions/after_action', $action_config, $request );
				}
			}
		}

		do_action( 'zionbuilder/rest/bulk_actions/after_get_items', $request );

		return rest_ensure_response( $response );
	}

	/**
	 * @param array $config
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function render_element( $config ) {
		$element_data     = $config['element_data'];
		$elements_manager = Plugin::$instance->elements_manager;
		$element_instance = $elements_manager->get_element_instance_with_data( $element_data );

		if ( $element_instance ) {
			ob_start();
			$element_instance->server_render( $config );
			$element_data = ob_get_clean();

			global $wp_scripts, $wp_styles;

			do_action( 'wp_enqueue_scripts' );

			$scripts = $this->get_dependency_config( $wp_scripts );
			$styles  = $this->get_dependency_config( $wp_styles );

			$combined_scripts = array_merge( $scripts, $styles );

			return rest_ensure_response(
				[
					'element' => $element_data,
					'scripts' => $combined_scripts,
				]
			);
		}

		return rest_ensure_response(
			[
				'element' => '',
			]
		);
	}

	/**
	 * Returns the script dependency configuration
	 *
	 * @param \WP_Scripts $manager
	 *
	 * @return array<string, mixed>
	 */
	private function get_dependency_config( $manager ) {
		$scripts = [];

		// Set all scripts
		$manager->all_deps( $manager->queue );
		foreach ( $manager->to_do as $handle ) {
			// Don't proceed if the script was not registered
			if ( ! isset( $manager->registered[$handle] ) ) {
				continue;
			}

			$obj = $manager->registered[$handle];
			$src = $obj->src;

			if ( $src ) {
				$scripts[$handle] = $this->prepare_script_data( $handle, $obj, $manager );
			}
		}

		return $scripts;
	}

	/**
	 * Returns the scripts data
	 *
	 * @param string          $handle  The script id
	 * @param \_WP_Dependency $obj
	 * @param \WP_Scripts     $manager
	 *
	 * @return array<string, mixed>
	 */
	private function prepare_script_data( $handle, $obj, $manager ) {
		if ( null === $obj->ver ) {
			$ver = '';
		} else {
			$ver = $obj->ver ? $obj->ver : get_bloginfo( 'version' );
		}

		if ( isset( $manager->args[$handle] ) ) {
			$ver = $ver ? $ver . '&amp;' . $manager->args[$handle] : $manager->args[$handle];
		}

		return [
			'handle' => $handle,
			'src'    => $this->compile_script_url(
				$handle,
				$manager->registered[$handle]->src,
				$manager->base_url,
				$manager->content_url,
				$ver
			),

			'data'   => $manager->get_data( $handle, 'data' ),
			'before' => $manager->get_data( $handle, 'before' ),
			'after'  => $manager->get_data( $handle, 'after' ),
		];
	}


	/**
	 * Returns the formatter script url
	 *
	 * @param string $handle      The script id/handle
	 * @param string $src         The script relative URL
	 * @param string $base_url    The script base URL
	 * @param string $content_url The script base URL
	 * @param string $ver         The script version
	 *
	 * @return string
	 */
	private function compile_script_url( $handle, $src, $base_url, $content_url, $ver ) {
		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $content_url && 0 === strpos( $src, $content_url ) ) ) {
			$src = $base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		/* This filter is documented in wp-includes/class.wp-scripts.php */
		return esc_url( apply_filters( 'script_loader_src', $src, $handle ) );
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

	public function get_input_select_options( $config, $request ) {
		if ( ! isset( $config['server_callback_method'] ) ) {
			return new \WP_Error( 'callback_method_missing', 'Missing callback_method param', [ 'status' => 400 ] );
		}

		$action_name = sprintf( 'zionbuilder/api/bulk_actions/get_input_select_options/%s', $config['server_callback_method'] );
		return rest_ensure_response( apply_filters( $action_name, [], $config, $request ) );
	}

	public function search_posts( $config ) {
		$keyword = isset( $config['keyword'] ) ? $config['keyword'] : '';

		$posts_query = new \WP_Query(
			[
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $keyword,
			]
		);

		return rest_ensure_response(
			array_map(
				function( $post ) {
					$post_type = get_post_type_object( get_post_type( $post ) );

					return [
						'url'        => get_permalink( $post->ID ),
						'post_title' => sprintf( '%s (%s)', $post->post_title, $post_type->labels->singular_name ),
					];
				},
				$posts_query->posts
			)
		);

	}
}
