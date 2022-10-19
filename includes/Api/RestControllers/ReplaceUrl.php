<?php

namespace ZionBuilder\Api\RestControllers;

use ZionBuilder\Plugin;
use ZionBuilder\Api\RestApiController;
use ZionBuilder\Post\BasePostType;
use ZionBuilder\Settings;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ReplaceUrl
 *
 * @package ZionBuilder\Api\RestControllers
 */
class ReplaceUrl extends RestApiController {

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
	protected $base = 'replace-url';

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
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'replace_item' ],
					'args'                => [
						'find'    => [
							'required' => true,
						],
						'replace' => [
							'required' => true,
						],
					],
					'permission_callback' => [ $this, 'replace_item_permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Checks if a given request has access to replace a specified url.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 *
	 * @return \WP_Error|bool true if the request has read access for the item, WP_Error object otherwise
	 */
	public function replace_item_permissions_check( $request ) {
		if ( ! $this->userCan( $request ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this resource.', 'zionbuilder' ), [ 'status' => $this->authorization_status_code() ] );
		}

		return true;
	}

	/**
	 * This function will replace a specified URL with a new one from page builder data and options
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return string|\WP_REST_Response|mixed
	 */
	public function replace_item( $request ) {
		$find    = $request->get_param( 'find' );
		$replace = $request->get_param( 'replace' );

		if ( $find === $replace ) {
			return new \WP_Error( 'replace_url_failed', esc_html__( 'Cannot replace URL\'s. They are the same.', 'zionbuilder' ) );
		}

		$is_valid_urls = filter_var( $find, FILTER_VALIDATE_URL ) && filter_var( $replace, FILTER_VALIDATE_URL );
		if ( ! $is_valid_urls ) {
			return new \WP_Error( 'replace_url_failed', esc_html__( 'Provided URL\'s are not valid.', 'zionbuilder' ) );
		}

		// Secure sql input
		$find    = str_replace( '/', '\\\/', trim( $find ) );
		$replace = str_replace( '/', '\\\/', trim( $replace ) );

		$meta_fields_to_replace = apply_filters(
			'zionbuilder/options_utils/replace_urls_meta_fields',
			[
				BasePostType::PAGE_TEMPLATE_META_KEY,
			]
		);

		foreach ( $meta_fields_to_replace as $meta_field ) {
			$replace_urls = $this->replace_urls( $find, $replace, $meta_field );

			if ( is_wp_error( $replace_urls ) ) {
				$replace_urls->add_data( [ 'status' => 500 ] );
				return $replace_urls;
			}
		}

		do_action( 'zionbuilder/options_utils/replace_urls', $find, $replace );

		// Replace in Page builder options
		$replace_urls_from_options = $this->replace_urls_from_options( $find, $replace, Settings::SETTINGS_OPTION_KEY );
		if ( is_wp_error( $replace_urls_from_options ) ) {
			$replace_urls_from_options->add_data( [ 'status' => 500 ] );
			return $replace_urls_from_options;
		}

		// Clear cache
		Plugin::instance()->assets->compile_global_css();

		return rest_ensure_response(
			[
				'message' => esc_html__( 'URLs successfully replaced!', 'zionbuilder' ),
			]
		);
	}

	/**
	 * This function will replace the URLs for a specific meta key
	 *
	 * @param string $find     -> url to be replaced
	 * @param string $replace  -> the new url
	 * @param string $meta_key -> the template data meta key
	 *
	 * @return true|\WP_Error
	 */
	public function replace_urls( $find, $replace, $meta_key ) {
		global $wpdb;

		// @codingStandardsIgnoreStart `$wpdb->prepare` will remove the backslashes when it's used
		$rows_affected = $wpdb->query(
			"UPDATE {$wpdb->postmeta} " .
			"SET `meta_value` = REPLACE(`meta_value`, '" . $find . "', '" . $replace . "') " .
			"WHERE `meta_key` = '" . $meta_key . "'"
		);
		// @codingStandardsIgnoreEnd

		if ( false === $rows_affected ) {
			return new \WP_Error( 'replace_url_failed', esc_html__( 'An error has occurred while updating the urls.', 'zionbuilder' ) );
		}
		return true;
	}

	/**
	 * This function will replace URLs from page builder options
	 *
	 * @param string $find        -> url to be replaced
	 * @param string $replace     -> the new url
	 * @param string $option_name -> the zion builder options key name
	 *
	 * @return \WP_Error|bool Boolean true on success
	 */
	public function replace_urls_from_options( $find, $replace, $option_name ) {
		global $wpdb;

		// @codingStandardsIgnoreStart `$wpdb->prepare` will remove the backslashes when it's used
		$rows_affected = $wpdb->query(
			"UPDATE {$wpdb->options} " .
			"SET `option_value` = REPLACE(`option_value`, '" . $find . "', '" . $replace . "') " .
			"WHERE `option_name` = '" . $option_name . "';"
		);
		// @codingStandardsIgnoreEnd

		if ( false === $rows_affected ) {
			return new \WP_Error( 'replace_url_failed', esc_html__( 'An error has occurred while updating the urls from page builder options.', 'zionbuilder' ) );
		}
		return true;
	}
}
