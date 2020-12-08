<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class DownloadArchive
 *
 * @package ZionBuilder\Api\RestControllers
 */
class DownloadArchive extends RestApiController {

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
	protected $base = 'download';

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
					'callback'            => [ $this, 'download_item' ],
					'args'                => [
						'name'     => [
							'required' => true,
						],
						'nonce'    => [
							'required' => true,
						],
						'_wpnonce' => [
							'required' => true,
						],
					],
					'permission_callback' => [ $this, 'download_item_permissions_check' ],
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
	public function download_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * This function will import a template and return a notification message
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error on error, otherwise it will exit the program
	 */
	public function download_item( $request ) {
		$file_name = $request->get_param( 'name' ) ? $request->get_param( 'name' ) : false;
		$nonce     = $request->get_param( 'nonce' ) ? $request->get_param( 'nonce' ) : false;

		if ( ! $file_name || ! $nonce ) {
			return new \WP_Error( 'Bad request', esc_html__( 'Invalid nonce or filename', 'zionbuilder' ), [ 'status' => '400' ] );
		}

		$import_export = Plugin::instance()->import_export;
		$download      = $import_export->download_template( $file_name );
		$download->add_data( [ 'status' => 500 ] );
		return $download;
	}
}
