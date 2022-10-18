<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Assets as ZBAssets;
use ZionBuilder\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Assets
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Assets extends RestApiController {

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
	protected $base = 'assets';

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
			'/' . $this->base . '/regenerate',
			[
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'regenerate' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/finish',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'finish' ],
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
	 * @return array|\WP_Error
	 */
	public function get_item( $request ) {
		$post_ids = Utils::get_builder_pages();
		$defaults = [
			[
				'type' => 'global_css',
			],
		];
		$files    = [];

		if ( $post_ids ) {
			$files = array_map(
				function ( $id ) {
					return [
						'type' => 'post',
						'id'   => $id,
					];
				},
				$post_ids
			);
		}

		return array_merge( $defaults, $files );
	}

	public function regenerate( $request ) {
		$type = $request->get_param( 'type' );

		switch ( $type ) {
			case 'global_css':
				return ZBAssets::compile_global_css();
			case 'post':
				return ZBAssets::generate_post_assets( $request->get_param( 'id' ) );
		}
	}

	public function finish() {
		delete_option( ZBAssets::REGENERATE_CACHE_FLAG );
	}
}
