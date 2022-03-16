<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;
use ZionBuilder\Whitelabel;
use ZionBuilder\FontsManager\Fonts\LocalGoogleFonts;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class RegenerateCache
 *
 * @package ZionBuilder\Api\RestControllers
 */
class RegenerateCache extends RestApiController {

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
	protected $base = 'regenerate-cache';

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
	 *
	 * @return array|\WP_Error
	 */
	public function get_item( $request ) {
		// Delete css/js cache
		$delete_css_cache = Plugin::instance()->cache->delete_all_cache();
		// Delete local fonts css
		$delete_local_fonts_cache = LocalGoogleFonts::delete_cache();

		if ( ! $delete_css_cache || ! $delete_local_fonts_cache ) {
			return new \WP_Error( 'regenerate_cache_failed', esc_html__( 'Regenerate cache failed!', 'zionbuilder' ), [ 'status' => '500' ] );
		}

		return [
			'message' => sprintf( '%s data refreshed', Whitelabel::get_title() ),
		];
	}
}
