<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Nonces
 * @package ZionBuilder
 */
class Nonces {

	/**
	 * Holds a reference to the nonce field id
	 */
	const NONCE_FIELD_ID = 'zionbuilder_nonce';

	/**
	 * Holds a reference to the active editor nonce action
	 */
	const ACTIVE_EDITOR = 'zionbuilder-editor-active';

	/**
	 * Holds a reference to the editor preview frame
	 */
	const EDITOR_PREVIEW_FRAME = 'zionbuilder-editor-preview-frame';

	/**
	 * Holds a reference to the general ajax nonce
	 */
	const GENERAL_AJAX_NONCE = 'zionbuilder-general-nonce';

	/**
	 * Holds a reference to the ajax action
	 */
	const AJAX_ACTION = 'zionbuilder-ajax';

	/**
	 * Holds a reference to the ajax action
	 */
	const REST_API = 'wp_rest';

	/**
	 * Generate nonce
	 *
	 * Will generate a nonce for a given action
	 *
	 * @param string $action
	 *
	 * @return bool|string
	 */
	public static function generate_nonce( $action ) {
		return wp_create_nonce( $action );
	}

	/**
	 * Verify nonce
	 *
	 * @param string $action The nonce action we need to verify
	 *
	 * @return int|false
	 */
	public static function verify_nonce( $action ) {
		if ( ! isset( $_REQUEST[self::NONCE_FIELD_ID] ) ) {
			return false;
		}
		return wp_verify_nonce( $_REQUEST[self::NONCE_FIELD_ID], $action );
	}

	/**
	 * Nonces constructor.
	 */
	private function __construct() {
	}
}
