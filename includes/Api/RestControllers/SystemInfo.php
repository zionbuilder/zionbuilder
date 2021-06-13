<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Api\RestApiController;
use ZionBuilder\Whitelabel;
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class SystemInfo
 *
 * @package ZionBuilder\Api\RestControllers
 */
class SystemInfo extends RestApiController {

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
	protected $base = 'system-info';

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
	 * Checks if a given request has access to read a system info.
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

	public function get_item( $request ) {
		// Get all data sets
		$result = [
			[
				'category_name' => esc_html__( 'Server environment', 'zionbuilder' ),
				'category_id'   => 'server_environment',
				'values'        => [
					'operating_system'   => [
						'name'  => esc_html__( 'operating system', 'zionbuilder' ),
						'value' => PHP_OS,
					],
					'software'           => [
						'name'  => esc_html__( 'software', 'zionbuilder' ),
						'value' => $_SERVER['SERVER_SOFTWARE'],
					],
					'mysql_version'      => $this->get_mysql_version(),
					'php_version'        => $this->get_php_version(),
					'php_max_input_vars' => [
						'name'  => esc_html__( 'php max input vars', 'zionbuilder' ),
						'value' => ini_get( 'max_input_vars' ),
					],
					'php_max_post_size'  => [
						'name'  => esc_html__( 'php max post size', 'zionbuilder' ),
						'value' => ini_get( 'post_max_size' ),
					],
					'gd_installed'       => $this->gd_installed(),
					'zip_installed'      => $this->zip_installed(),
					'write_permissions'  => $this->get_write_permissions(),
					// 'zion_library'       => $this->get_zion_library(),
				],
			],
			[
				'category_name' => esc_html__( 'WordPress environment', 'zionbuilder' ),
				'category_id'   => 'wordpress_environment',
				'values'        => [
					'wordpress_version'   => $this->get_wordpress_version(),
					'site_url'            => [
						'name'  => esc_html__( 'site url', 'zionbuilder' ),
						'value' => get_site_url(),
					],
					'home_url'            => [
						'name'  => esc_html__( 'home url', 'zionbuilder' ),
						'value' => get_home_url(),
					],
					'wp_multisite'        => [
						'name'  => esc_html__( 'wp multisite', 'zionbuilder' ),
						'value' => is_multisite() ? esc_html__( 'Yes', 'zionbuilder' ) : esc_html__( 'No', 'zionbuilder' ),
					],
					'max_upload_size'     => [
						'name'  => esc_html__( 'max upload size', 'zionbuilder' ),
						'value' => size_format( wp_max_upload_size() ),
					],
					'memory_limit'        => $this->get_memory_limit(),
					'permalink_structure' => $this->get_permalink_structure(),
					'language'            => [
						'name'  => esc_html__( 'language', 'zionbuilder' ),
						'value' => get_bloginfo( 'language' ),
					],
					'timezone'            => $this->get_timezone(),
					'admin_email'         => [
						'name'  => esc_html__( 'admin email', 'zionbuilder' ),
						'value' => get_option( 'admin_email' ),
					],
					'debug_mode'          => [
						'name'  => esc_html__( 'debug mode', 'zionbuilder' ),
						'value' => WP_DEBUG ? esc_html__( 'Active', 'zionbuilder' ) : esc_html__( 'Inactive', 'zionbuilder' ),
					],
				],
			],
			[
				'category_name' => esc_html__( 'Theme', 'zionbuilder' ),
				'category_id'   => 'theme_info',
				'values'        => [
					'theme'         => [
						'name'  => esc_html__( 'name', 'zionbuilder' ),
						'value' => wp_get_theme()->get( 'Name' ),
					],
					'theme_version' => [
						'name'  => esc_html__( 'version', 'zionbuilder' ),
						'value' => wp_get_theme()->get( 'Version' ),
					],
					'theme_author'  => [
						'name'  => esc_html__( 'author', 'zionbuilder' ),
						'value' => wp_get_theme()->get( 'Author' ),
					],
					'child_theme'   => $this->has_child_theme(),
				],
			],
			[
				'category_name' => esc_html__( 'Active plugins', 'zionbuilder' ),
				'category_id'   => 'plugins_info',
				'values'        => $this->get_active_plugins(),
			],
		];

		return rest_ensure_response( $result );
	}

	/**
	 * This function will return an array with information regarding php version and version requirments
	 *
	 * @return array {
	 *
	 * @var string value   -> PHP version
	 * @var string icon    -> info icon
	 * @var string message -> warning message if the PHP version is too low
	 *             }
	 */
	public function get_php_version() {
		$info          = [];
		$info['name']  = esc_html__( 'php version', 'zionbuilder' );
		$info['value'] = PHP_VERSION;
		if ( version_compare( $info['value'], '7.0.0', '<' ) ) {
			$info['icon']    = 'warning';
			$info['message'] = esc_html__( 'We recommend using php version 7.0.0 or higher. By using PHP version 7 or above, your site will increase in speed.', 'zionbuilder' );
		} else {
			$info['icon'] = 'ok';
		}
		return $info;
	}

	/**
	 * This function will return an array with informations regarding the MySql version
	 *
	 * @return array{name: string, value: string}
	 */
	public function get_mysql_version() {
		global $wpdb;
		$info          = [];
		$info['name']  = esc_html__( 'mysql version', 'zionbuilder' );
		$db_version    = $wpdb->get_results( "SHOW VARIABLES WHERE `Variable_name` IN ( 'version_comment', 'innodb_version' )", OBJECT_K );
		$info['value'] = $db_version['version_comment']->Value . ' v' . $db_version['innodb_version']->Value;
		return $info;
	}

	/**
	 * This function will check if the gd library is installed
	 *
	 * @return array {
	 *
	 * @var string value   -> yes/no depending on the activation status
	 * @var string icon    -> info icon
	 * @var string message -> warning message if the extension is not installed
	 *             }
	 */
	public function gd_installed() {
		$info         = [];
		$info['name'] = esc_html__( 'gd installed', 'zionbuilder' );
		$gd_installed = extension_loaded( 'gd' );
		if ( $gd_installed ) {
			$info['value'] = esc_html__( 'Yes', 'zionbuilder' );
			$info['icon']  = 'ok';
		} else {
			$info['value']   = esc_html__( 'No', 'zionbuilder' );
			$info['icon']    = 'not_ok';
			$info['message'] = esc_html__( 'The GD extension is not installed!', 'zionbuilder' );
		}
		return $info;
	}

	/**
	 * This function will check if the ZIP extension is installed
	 *
	 * @return array {
	 *
	 * @var string value   -> yes/no depending on the activation status
	 * @var string icon    -> info icon
	 * @var string message -> warning message if the extension is not installed
	 *             }
	 */
	public function zip_installed() {
		$info         = [];
		$info['name'] = esc_html__( 'zip installed', 'zionbuilder' );
		if ( extension_loaded( 'zip' ) ) {
			$info['value'] = esc_html__( 'Yes', 'zionbuilder' );
			$info['icon']  = 'ok';
		} else {
			$info['value']   = esc_html__( 'No', 'zionbuilder' );
			$info['icon']    = 'not_ok';
			$info['message'] = esc_html__( 'The zip extension is not installed!', 'zionbuilder' );
		}
		return $info;
	}

	/**
	 * This function will check if the required folders has writing permissions
	 *
	 * @return array {
	 *
	 * @var string $value -> show the permissions status
	 * @var string $message -> will display a message with the permissions issues
	 * @var string $icon -> will render the icon specific to permissions status
	 *             }
	 */
	public function get_write_permissions() {
		$info           = [];
		$write_problems = [];
		$paths          = [
			ABSPATH => esc_html__( 'WordPress root directory', 'zionbuilder' ),
		];
		$info['name']   = esc_html__( 'write permissions', 'zionbuilder' );

		$wp_upload_dir = wp_upload_dir();

		if ( $wp_upload_dir['error'] ) {
			$write_problems[] = esc_html__( 'WordPress root uploads directory', 'zionbuilder' );
		}

		$zion_uploads_path = $wp_upload_dir['basedir'] . '/zionbuilder';

		if ( is_dir( $zion_uploads_path ) ) {
			$paths[$zion_uploads_path] = sprintf( '%s uploads directory', Whitelabel::get_title() );
		}

		$htaccess_file = ABSPATH . '/.htaccess';

		if ( file_exists( $htaccess_file ) ) {
			$paths[$htaccess_file] = esc_html__( '.htaccess file', 'zionbuilder' );
		}

		foreach ( $paths as $dir => $description ) {
			if ( ! is_writable( $dir ) ) {
				$write_problems[] = $description;
			}
		}

		if ( $write_problems ) {
			$info['value']    = esc_html__( 'Not all right', 'zionbuilder' );
			$info['icon']     = 'not_ok';
			$info['message']  = esc_html__( 'There are some writing permissions issues with the following directories/files: ', 'zionbuilder' );
			$info['message'] .= implode( "\n\t\t - ", $write_problems );
		} else {
			$info['value'] = esc_html__( 'All right', 'zionbuilder' );
			$info['icon']  = 'ok';
		}

		return $info;
	}

	/**
	 * Check if zion library is active
	 *
	 * @return array $info
	 */

	/**
	 * Check to see whether or not the plugin is connected to the Zion Library
	 *
	 * @return array
	 */
	public function get_zion_library() {
		$info         = [];
		$info['name'] = sprintf( '%s library', Whitelabel::get_title() );
		if ( post_type_exists( 'zionbuilder_library' ) ) {
			$info['value'] = esc_html__( 'Connected', 'zionbuilder' );
			$info['icon']  = 'ok';
		} else {
			$info['value']   = esc_html__( 'Disconnected', 'zionbuilder' );
			$info['icon']    = 'not_ok';
			$info['message'] = sprintf( 'The %s library is disconnected!', Whitelabel::get_title() );
		}
		return $info;
	}

	/**
	 * This function will return the WordPress version information
	 *
	 * @return array {
	 *
	 * @var string value   -> WordPress version
	 * @var string icon    -> info icon
	 * @var string message -> recommendation message
	 *             }
	 */
	public function get_wordpress_version() {
		$info          = [];
		$info['name']  = esc_html__( 'version', 'zionbuilder' );
		$info['value'] = get_bloginfo( 'version' );

		if ( version_compare( $info['value'], '4.9.8', '>=' ) ) {
			$info['icon'] = 'ok';
		} else {
			$info['icon']    = 'warning';
			$info['message'] = esc_html__( 'We recommend you to use WordPress version 4.9.8 or higher', 'zionbuilder' );
		}
		return $info;
	}

	/**
	 * This function verify the memory limit and compare it with the recommended memory limit
	 *
	 * @return array{name: string, value: string, icon: string, message: ?string}
	 */
	public function get_memory_limit() {
		$info               = [];
		$info['name']       = esc_html__( 'memory limit', 'zionbuilder' );
		$info['value']      = WP_MEMORY_LIMIT;
		$info['icon']       = 'ok';
		$recommended_memory = '64M';

		if ( wp_convert_hr_to_bytes( WP_MEMORY_LIMIT ) < wp_convert_hr_to_bytes( $recommended_memory ) ) {
			$info['message'] = sprintf(
				/* translators: 1: recommended memory, 2: Codex URL with details regarding how you can increase memory limit */
				_x( 'We recommend setting memory to at least %1$s. For more information, read about <a target="_blank" href="%2$s">how to Increase memory allocated to PHP</a>.', 'System Info', 'zionbuilder' ),
				$recommended_memory,
				'https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP'
			);
			$info['icon'] = 'not_ok';
		} else {
			$info['icon'] = 'ok';
		}
		return $info;
	}

	/**
	 * This function retrieve the permalink structure
	 *
	 * @return array{name: string, value: string} The permalink structure
	 */
	public function get_permalink_structure() {
		global $wp_rewrite;
		$info          = [];
		$info['name']  = esc_html__( 'permalink structure', 'zionbuilder' );
		$info['value'] = $wp_rewrite->permalink_structure;

		if ( ! $info['value'] ) {
			$info['value'] = esc_html__( 'Plain', 'zionbuilder' );
		}

		return $info;
	}

	/**
	 * Retrieve the WordPress timezone
	 *
	 * @return array{name: string, value: string} The WordPress timezone
	 */
	public function get_timezone() {
		$info          = [];
		$info['name']  = esc_html__( 'timezone', 'zionbuilder' );
		$info['value'] = get_option( 'timezone_string' );
		if ( ! $info['value'] ) {
			$info['value'] = get_option( 'gmt_offset' );
		}
		return $info;
	}

	/**
	 * This function will check if the child theme is active
	 *
	 * @return array{name: string, value: string, icon: string, message: string} Yes/No depending on child theme status
	 */
	public function has_child_theme() {
		$has_child_theme = is_child_theme();
		$info            = [];
		$info['name']    = esc_html__( 'child theme', 'zionbuilder' );
		$info['value']   = $has_child_theme ? esc_html__( 'Yes', 'zionbuilder' ) : esc_html__( 'No', 'zionbuilder' );
		$info['icon']    = 'ok';

		if ( ! $has_child_theme ) {
			$info['icon']    = 'warning';
			$info['message'] = sprintf(
				/* translators: %s: Codex URL with child theme information */
				_x( 'If you want to modify the source code of your theme, we recommend using a <a target="_blank" href="%s">child theme</a>.', 'System Info', 'zionbuilder' ),
				'https://developer.wordpress.org/themes/advanced-topics/child-themes/'
			);
		}
		return $info;
	}

	/**
	 * This function will return the active plugins
	 *
	 * @return array<int, array{name: string, version: string, author: string, url: string}> Plugin details
	 */
	public function get_active_plugins() {

		// load the plugin.php if the function get_plugins doesn't exists
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins' );
		$plugins_list   = [];

		foreach ( $all_plugins as $plugin_id => $plugin_details ) {

			if ( in_array( $plugin_id, $active_plugins, true ) ) {
				if ( $plugin_details['Name'] === 'Zion Builder' || $plugin_details['Name'] === 'Zion Builder Pro' ) {
					$white_label_title = $plugin_details['Name'] === 'Zion Builder' ? Whitelabel::get_title() : sprintf( '%s Pro', Whitelabel::get_title() );
					$plugins_list[]    = [
						'name'    => $white_label_title,
						'version' => $plugin_details['Version'],
						'author'  => Whitelabel::get_title(),
						'url'     => Whitelabel::get_help_url(),
					];
				} else {
					$plugins_list[] = [
						'name'    => $plugin_details['Name'],
						'version' => $plugin_details['Version'],
						'author'  => $plugin_details['Author'],
						'url'     => $plugin_details['PluginURI'],
					];
				}
			}
		}

		return $plugins_list;
	}
}
