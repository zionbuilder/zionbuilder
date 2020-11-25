<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Utils;
use ZionBuilder\FileSystem;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class GoogleFonts
 *
 * @package ZionBuilder\Api\RestControllers
 */
class GoogleFonts extends RestApiController {

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
	protected $base = 'google-fonts';

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
		//#! TODO @Stefan: use the Google fonts from the DB instead (either using FontsManager/GoogleFonts or Settings:get('google_fonts'))
		//#! TODO @Stefan: make sure you check the usages, since the result differs from what you currently return from this function

		//
		// Google fonts
		$google_fonts_json = Utils::get_file_path( 'includes/data_sets/google_fonts.json' );
		$google_fonts      = json_decode( FileSystem::get_file_system()->get_contents( $google_fonts_json ), true );

		if ( null === $google_fonts['items'] ) {
			return new \WP_Error( 'json_error', esc_html__( 'Cannot extract Google fonts.', 'zionbuilder' ), [ 'status' => '500' ] );
		}

		if ( ! is_array( $google_fonts['items'] ) ) {
			return new \WP_Error( 'no_items', esc_html__( 'No Google fonts found.', 'zionbuilder' ), [ 'status' => '404' ] );
		}

		return rest_ensure_response( $google_fonts['items'] );
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array item schema data
	 */
	public function get_item_schema() {
		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => [
				'kind'     => [
					'type'        => 'string',
					'description' => __( 'Google font type', 'zionbuilder' ),
					'example'     => 'webfonts#webfont',
				],
				'family'   => [
					'type'        => 'string',
					'description' => __( 'Font family', 'zionbuilder' ),
				],
				'variants' => [
					'type' => 'array',
				],
				'subsets'  => [
					'type' => 'array',
				],
				'files'    => [
					'type' => 'object',
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}
}
