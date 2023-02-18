<?php

namespace ZionBuilder;

use ZionBuilder\CommonJS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Install
 *
 * @package ZionBuilder
 */
class Screenshot {
	const URL_ARGUMENT        = 'zionbuilder-generate-screenshot';
	const PROXY_URL_ARGUMENT  = 'zionbuilder-proxy';
	const PROXY_URL_NONCE_KEY = 'zionbuilder-proxy-nonce';
	const PROXY_ASSET_PARAM   = 'zionbuilder-asset';

	public function __construct() {
		if ( isset( $_GET[self::URL_ARGUMENT] ) && $_GET[self::URL_ARGUMENT] === '1' ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );
		}
		if ( Permissions::user_allowed_edit() && isset( $_GET[self::PROXY_URL_NONCE_KEY] ) && wp_verify_nonce( $_GET[self::PROXY_URL_NONCE_KEY], self::PROXY_URL_NONCE_KEY ) && isset( $_GET[self::PROXY_URL_ARGUMENT] ) && isset( $_GET[self::PROXY_ASSET_PARAM] ) ) {
			$this->print_proxy_asset( $_GET[self::PROXY_ASSET_PARAM] );
		}
	}

	public function print_proxy_asset( $asset ) {
		$response = wp_remote_get( utf8_decode( $asset ) );

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$headers = wp_remote_retrieve_headers( $response );

		header( 'content-type: ' . $headers['content-type'] );

		echo wp_remote_retrieve_body( $response ); // phpcs:ignore -- We output the asset data
		die;
	}

	public function on_enqueue_scripts() {
		// Load Scripts
		Plugin::instance()->scripts->enqueue_style(
			'zb-screenshot',
			'screenshot',
			[],
			Plugin::instance()->get_version()
		);

		// Load Scripts
		Plugin::instance()->scripts->enqueue_script(
			'zb-screenshot',
			'screenshot',
			[],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'zb-screenshot',
			'ZnPbScreenshotData',
			[
				'home_url'  => home_url(),
				'is_debug'  => Environment::is_debug(),
				'constants' => [
					'PROXY_URL_ARGUMENT'  => self::PROXY_URL_ARGUMENT,
					'PROXY_URL_NONCE_KEY' => self::PROXY_URL_NONCE_KEY,
					'PROXY_ASSET_PARAM'   => self::PROXY_ASSET_PARAM,
				],
				'assets'    => [
					'placeholder_iframe' => Utils::get_file_url( 'assets/img/frame.svg' ),
				],
				'nonce_key' => wp_create_nonce( self::PROXY_URL_NONCE_KEY ),
			]
		);
	}
}
