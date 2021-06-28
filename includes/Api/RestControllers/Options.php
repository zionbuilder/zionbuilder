<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Options
 *
 * @package ZionBuilder\Api\RestControllers
 */
class Options extends RestApiController {

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
	protected $base = 'options';

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
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
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
	 * Checks if a given request has access to read a data set.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function update_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to edit this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$options  = $this->get_options();
		$response = [];

		foreach ( $options as $key => $option_config ) {
			$saved_value = Settings::get_value( $key );

			// Fix for rest_sanitize_value_from_schema() that returns an array with 1 empty value
			// See: https://core.trac.wordpress.org/ticket/45448#ticket
			if ( 'array' === $option_config['schema']['type'] && ! is_array( $saved_value ) ) {
				$saved_value = preg_split( '/[\s,]+/', $saved_value, null, PREG_SPLIT_NO_EMPTY );
			}
			$response[$key] = rest_sanitize_value_from_schema( $saved_value, $option_config['schema'] );
		}

		return rest_ensure_response( $response );
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 */
	public function update_item( $request ) {
		$params = $request->get_params();

		// Update the settings
		Settings::save_settings( $params );
		return $this->get_item( $request );
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array item schema data
	 */
	public function get_item_schema() {
		$options = $this->get_options();

		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => [],
		];

		foreach ( $options as $option_id => $option ) {
			$schema['properties'][$option_id] = $option['schema'];
		}

		return $this->add_additional_fields_schema( $schema );
	}

	private function get_options() {
		$options = [];

		$options['maintenance_mode'] = [
			'name'   => 'Maintenance mode',
			'schema' => [
				'description' => __( 'Contains saved values for maintenance mode.', 'zionbuilder' ),
				'type'        => 'object',
			],
		];

		$options['google_fonts'] = [
			'name'   => 'Google Fonts',
			'schema' => [
				'description' => __( 'Contains saved value for Google fonts.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'font_family'   => [
							'type'    => 'string',
							'example' => 'Open Sans',
						],
						'font_subset'   => [
							'type'  => 'array',
							'items' => [
								'type' => 'string',
							],
						],
						'font_variants' => [
							'type'  => 'array',
							'items' => [
								'type' => 'string',
							],
						],
					],
				],
			],
		];

		$options['custom_fonts'] = [
			'name'   => 'Custom Fonts',
			'schema' => [
				'description' => __( 'Contains saved value for Custom fonts.', 'zionbuilder' ),
				'type'        => 'array',
				'default'     => [],
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'font_family' => [
							'type'    => 'string',
							'example' => 'My Custom Font',
						],
						'weight'      => [
							'type' => 'string',
						],
						'woff'        => [
							'type'   => 'string',
							'format' => 'uri',
						],
						'eot'         => [
							'type'   => 'string',
							'format' => 'uri',
						],
						'svg'         => [
							'type'   => 'string',
							'format' => 'uri',
						],
						'ttf'         => [
							'type'   => 'string',
							'format' => 'uri',
						],
					],
				],
			],
		];

		$options['typekit_token'] = [
			'name'   => 'Typekit token',
			'schema' => [
				'description' => __( 'Contains the value for the typekit token if it is set.', 'zionbuilder' ),
				'type'        => 'string',
			],
		];

		$options['typekit_fonts'] = [
			'name'   => 'Typekit Fonts',
			'schema' => [
				'description' => __( 'Contains saved value for typekit fonts.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type'     => 'string',
					'required' => false,
				],
				'default'     => [],
			],

		];

		$options['local_colors'] = [
			'name'   => 'Local Colors',
			'schema' => [
				'description' => __( 'Contains saved value for local colors.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type' => 'string',
				],
			],
		];

		$options['global_colors'] = [
			'name'   => 'Global Colors',
			'schema' => [
				'description' => __( 'Contains saved value for global colors.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type' => 'object',
				],
			],
		];

		$options['local_gradients']  = [
			'name'   => 'Local Gradients',
			'schema' => [
				'description' => __( 'Contains saved value for local gradients.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type' => 'object',
				],
			],
		];
		$options['global_gradients'] = [
			'name'   => 'Global Gradients',
			'schema' => [
				'description' => __( 'Contains saved value for global gradients.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type' => 'object',
				],
			],
		];

		$options['user_roles_permissions'] = [
			'name'   => 'Permissions',
			'schema' => [
				'description' => __( 'Contains saved value user roles permissions and user permissions.', 'zionbuilder' ),
				'type'        => 'object',
				'items'       => [
					'type' => 'object',
				],
			],
		];

		$options['users_permissions'] = [
			'name'   => 'Users Permissions',
			'schema' => [
				'description' => __( 'Contains saved value users permissions.', 'zionbuilder' ),
				'type'        => 'object',
				'items'       => [
					'type' => 'object',
				],
			],
		];

		$options['allowed_post_types'] = [
			'name'   => 'Allowed post types',
			'schema' => [
				'description' => __( 'Contains saved value for allowed post types.', 'zionbuilder' ),
				'type'        => 'array',
				'items'       => [
					'type' => 'string',
				],
			],
		];

		$options['white_label'] = [
			'name'   => 'White Label',
			'schema' => [
				'description' => __( 'Contains saved values for white label', 'zionbuilder' ),
				'type'        => 'object',
				'items'       => [
					'type' => 'object',
				],
			],
		];

		return apply_filters( 'zionbuilder/rest/options/schema', $options );
	}
}
