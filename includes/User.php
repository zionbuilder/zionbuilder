<?php

namespace ZionBuilder;

use WP_Error;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Templates
 *
 * @package ZionBuilder
 */
class User {
	const USER_META_ZION_DATA = '_zionbuilder_user_data';


	/**
	 * Saves the user data to the DB
	 *
	 * @param array $data User data as builder UI and responsive display
	 * @param int $user_id User id for which we need to save the values
	 *
	 * @return int|bool|WP_Error Meta ID if the key didn't exist, true on successful update, false on failure or if the value passed to the function is the same as the one that is already in the database
	 */
	public static function save_user_data( $data, $user_id = null ) {
		$user_id = null === $user_id ? get_current_user_id() : $user_id;

		if ( ! $user_id ) {
			return new WP_Error( 'user_not_found', \esc_html__( 'no active user found', 'zionbuilder' ) );
		}

		update_user_meta( $user_id, self::USER_META_ZION_DATA, wp_json_encode( $data ) );

		return true;
	}


	/**
	 * Returns the saved user meta values from DB
	 *
	 * @param int $user_id User ID.
	 *
	 * @return mixed The value of meta data field. An empty string if a valid but non-existing user ID is passed.
	 */
	public static function get_user_data( $user_id = null ) {
		$user_id = null === $user_id ? get_current_user_id() : $user_id;

		if ( ! $user_id ) {
			return new WP_Error( 'user_not_found', esc_html__( 'no active user found', 'zionbuilder' ) );
		}

		$user_data = \get_user_meta( $user_id, self::USER_META_ZION_DATA, true );

		return json_decode( $user_data, true );
	}
}
