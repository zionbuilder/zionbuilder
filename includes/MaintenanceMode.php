<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Options\Options;
use ZionBuilder\Settings;
use ZionBuilder\Templates;


// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class MaintenanceMode
 *
 * Will handle all interactions with the WordPress admin area and the page builder
 *
 * @package ZionBuilder
 */
class MaintenanceMode {
	/**
	 * Main class constructor
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'zionbuilder/admin/initial_data', [ $this, 'add_admin_page_data' ] );
		}

		// Only run if the maintenance mode is enabled
		if ( ! empty( $this->get_value( 'mode' ) ) ) {
			add_action( 'template_redirect', [ $this, 'on_template_redirect' ], 98 ); // use 98 as the builder uses 99
		}
	}

	/**
	 * Returns a saved value for the maintenance mode
	 *
	 * @param string $setting_key The key for the value to be returned
	 * @param mixed $default The value that needs to be returned if no existing value is saved
	 *
	 * @since 2.6.0
	 *
	 * @return mixed
	 */
	public function get_value( $setting_key, $default = null ) {
		$saved_values = Settings::get_value( 'maintenance_mode', [] );

		if ( isset( $saved_values[$setting_key] ) ) {
			return $saved_values[$setting_key];
		}

		return $default;
	}

	/**
	 * Hooks into 'template_redirect' in order to replace the current post with the choosen template
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */
	public function on_template_redirect() {
		global $post;

		if ( Plugin::$instance->editor->preview->is_preview_mode() ) {
			return;
		}

		$saved_template_id = $this->get_value( 'template' );

		// Only proceed if we have values
		if ( empty( $saved_template_id ) ) {
			return;
		}

		// Check for logged in users
		$allow_logged_in_users = $this->get_value( 'allow_logged_in_users', 'yes' );
		if ( $allow_logged_in_users === 'yes' && is_user_logged_in() ) {
			return;
		}

		// Check for user ip
		$allowed_user_ips = $this->get_value( 'allow_specific_ips', '' );
		if ( ! empty( $allowed_user_ips ) && is_user_logged_in() ) {
			$allowed_ips = explode( ',', $allowed_user_ips );
			$visitor_ip  = $this->get_user_ip();

			if ( in_array( $visitor_ip, $allowed_ips, true ) ) {
				return;
			}
		}

		// Check for user roles
		$allowe_user_roles = $this->get_value( 'allow_specific_user_roles', [] );
		$current_user      = wp_get_current_user();
		$user_roles        = (array) $current_user->roles;

		// Check if the user has access to the pagebuilder
		foreach ( $user_roles as $role_id ) {
			if ( in_array( $role_id, $allowe_user_roles, true ) ) {
				return;
			}
		}

		if ( 'maintenance_mode' === $this->get_value( 'mode' ) ) {
			$protocol = wp_get_server_protocol();
			header( "$protocol 503 Service Unavailable", true, 503 );

			// Return search engines after an hour
			header( 'Retry-After: 3600' );
		}

		// We need to overwrite the global post. WP will do the rest
		$post = get_post( $saved_template_id );  // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		// set the proper queried_object
		query_posts( // phpcs:ignore WordPress.WP.DiscouragedFunctions
			[
				'p'         => $saved_template_id,
				'post_type' => Templates::TEMPLATE_POST_TYPE,
			]
		);
	}


	/**
	 * Returns the visitor ip address
	 *
	 * @return string
	 */
	public function get_user_ip() {
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			if ( strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) > 0 ) {
				$addr = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
				return trim( $addr[0] );
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Adds the maintenance mode options schema to the admin data
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function add_admin_page_data( $data ) {
		$data['maintenance_mode'] = [
			'schema' => $this->get_options_schema(),
		];

		return $data;
	}

	/**
	 * Returns the maintenance mode options schema
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function get_options_schema() {
		$options_schema = new Options( 'zionbuilder/schema/maintenance_mode' );

		$options_schema->add_option(
			'mode',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Select mode', 'zionbuilder' ),
				'description' => esc_html__( 'Depending on the mode selected, your website will return a HTTP 200 response ( coming soon ) or a HTTP 503 mode ( maintenance mode )', 'zionbuilder' ),
				'default'     => '',
				'options'     => [
					[
						'name' => esc_html__( 'Disabled', 'zionbuilder' ),
						'id'   => '',
					],
					[
						'name' => esc_html__( 'Coming Soon', 'zionbuilder' ),
						'id'   => 'coming_soon',
					],
					[
						'name' => esc_html__( 'Maintenance mode', 'zionbuilder' ),
						'id'   => 'maintenance_mode',
					],
				],
			]
		);

		$templates        = Plugin::instance()->templates->get_templates_by_type( 'template' );
		$template_options = [];

		if ( is_array( $templates ) ) {
			$template_options = array_map(
				function( $post ) {
					return [
						'id'   => $post->ID,
						'name' => $post->post_title,
					];
				},
				$templates
			);
		}

		$options_schema->add_option(
			'template',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Select template', 'zionbuilder' ),
				'description' => esc_html__( 'Choose the template you want to use as your maintenance/coming soon page.', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Select template', 'zionbuilder' ),
				'options'     => $template_options,
				'dependency'  => [
					[
						'option' => 'mode',
						'value'  => [ 'coming_soon', 'maintenance_mode' ],
					],
				],
			]
		);

		$options_schema->add_option(
			'allow_logged_in_users',
			[
				'type'       => 'select',
				'title'      => esc_html__( 'Allow logged in users to view the website', 'zionbuilder' ),
				'default'    => 'yes',
				'options'    => [
					[
						'name' => esc_html__( 'Yes', 'zionbuilder' ),
						'id'   => 'yes',
					],
					[
						'name' => esc_html__( 'No', 'zionbuilder' ),
						'id'   => 'no',
					],
				],
				'dependency' => [
					[
						'option' => 'mode',
						'value'  => [ 'coming_soon', 'maintenance_mode' ],
					],
				],
			]
		);

		global $wp_roles;
		$user_roles = $wp_roles->role_names;
		$roles_list = [];

		foreach ( $user_roles as $role_key => $role_name ) {
			$roles_list[] = [
				'id'   => $role_key,
				'name' => $role_name,
			];
		}

		$options_schema->add_option(
			'allow_specific_user_roles',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Allow specific user roles to view the website', 'zionbuilder' ),
				'multiple'    => true,
				'options'     => $roles_list,
				'placeholder' => esc_html__( 'Select user roles', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'mode',
						'value'  => [ 'coming_soon', 'maintenance_mode' ],
					],
				],
			]
		);

		$options_schema->add_option(
			'allow_specific_ips',
			[
				'type'        => 'textarea',
				'title'       => esc_html__( "Allow specific IP's to access the website", 'zionbuilder' ),
				'description' => esc_html__( "Enter a list of ip's separated by a comma", 'zionbuilder' ),
				'placeholder' => esc_html__( '192.168.1.1, 127.0.0.1', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'mode',
						'value'  => [ 'coming_soon', 'maintenance_mode' ],
					],
				],
			]
		);

		return $options_schema->get_schema();
	}
}
