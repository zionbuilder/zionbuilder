<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class DataSets
 *
 * @package ZionBuilder\Api\RestControllers
 */
class DataSets extends RestApiController {

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
	protected $base = 'data-sets';

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
			'/' . $this->base . '/user_roles',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_user_roles_item' ],
					'args'                => [],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/post_types',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_post_types_item' ],
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
	 * This function will return all the data-sets
	 * Endpoint: /zionbuilder/v1/data-sets
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$data_sets = [
			'fonts_list' => Plugin::$instance->fonts_manager->get_data_sets(),
			'user_roles' => $this->get_user_roles(),
			'post_types' => $this->format_post_types(),
			'taxonomies' => $this->format_taxonomies(),
			'icons'      => $this->get_icons_list(),
		];
		return rest_ensure_response( $data_sets );
	}

	/**
	 * This function will return the user roles data sets
	 * Endpoint: /zionbuilder/v1/user_roles
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return array|\WP_REST_Response
	 */
	public function get_user_roles_item( $request ) {
		global $wp_roles;
		$result = $this->get_user_roles();

		return rest_ensure_response( $result );
	}

	/**
	 * This function will return the user roles id and name
	 *
	 * @return array<int, array{id: string, name: string}>
	 */
	public function get_user_roles() {
		global $wp_roles;
		$user_roles = $wp_roles->role_names;
		$roles_list = [];

		foreach ( $user_roles as $role_key => $role_name ) {
			if ( 'administrator' === $role_key ) {
				continue;
			}
			$roles_list[] = [
				'id'   => $role_key,
				'name' => $role_name,
			];
		}

		return $roles_list;
	}

	/**
	 * Retrieve the list of icons
	 *
	 * @return array|null
	 */
	public function get_icons_list() {
		return Plugin::$instance->icons->get_icons_list();
	}

	/**
	 * This function will return the post types
	 * Endpoint: /zionbuilder/v1/post_types
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return array|\WP_REST_Response
	 */
	public function get_post_types_item( $request ) {
		$result = $this->format_post_types();

		return rest_ensure_response( $result );
	}

	/**
	 * This function will format the available post types by name and ID
	 *
	 * @return array will all post types
	 */
	public function format_post_types() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		$post_types_list = [];

		foreach ( $post_types as $name => $post_type ) {
			$post_types_list[] = [
				'id'   => $name,
				'name' => $post_type->label,
			];
		}

		return apply_filters( 'zionbuilder/data_sets/post_types', $post_types_list );
	}


	/**
	 * Converts the list of taxonomies to the structure accepted by the select option
	 *
	 * @return array
	 */
	public function format_taxonomies() {
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

		$taxonomies_list = [];

		foreach ( $taxonomies as $name => $taxonomy ) {
			$taxonomies_list[] = [
				'id'   => $name,
				'name' => $taxonomy->label,
			];
		}

		return apply_filters( 'zionbuilder/data_sets/taxonomies', $taxonomies_list );
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
			'title'      => 'data-sets',
			'type'       => 'object',
			'properties' => [
				'fonts_list' => [
					'type'        => 'array',
					'description' => __( 'Combined fonts list', 'zionbuilder' ),

				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}
}
