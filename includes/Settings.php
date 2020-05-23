<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Settings
 *
 * @package ZionBuilder
 */
class Settings {
	const SETTINGS_OPTION_KEY = '_zionbuilder_options';

	private static $saved_values = null;

	private static $default_options = [
		'allowed_post_types' => [ 'post', 'page' ],
	];

	/**
	 * Restore Defaults
	 *
	 * Restores the options to default values
	 *
	 * @return boolean true if option value has changed, false if not or if update failed
	 */
	public static function restore_defaults() {
		return update_option( self::SETTINGS_OPTION_KEY, wp_json_encode( self::$default_options ) );
	}

	/**
	 * Get value
	 *
	 * Returns a value from DB
	 *
	 * @param string $setting_key
	 * @param mixed  $default     The default value to return if no value was saved for the requested option
	 *
	 * @return null|mixed
	 */
	public static function get_value( $setting_key, $default = null ) {
		$all_settings = self::get_all_values();

		if ( isset( $all_settings[$setting_key] ) ) {
			return $all_settings[$setting_key];
		}

		return $default;
	}


	/**
	 * Get all values
	 *
	 * Returns all saved values from DB
	 *
	 * @return mixed|array
	 */
	public static function get_all_values() {
		if ( null === self::$saved_values ) {
			$saved_values       = get_option( self::SETTINGS_OPTION_KEY );
			self::$saved_values = json_decode( $saved_values, true );
		}

		return self::$saved_values;
	}

	/**
	 * Clear cache
	 *
	 * Clears the options cache
	 *
	 * @return void
	 */
	public static function clear_cache() {
		self::$saved_values = null;
	}


	/**
	 * Save Settings
	 *
	 * Will update the SETTINGS_OPTION_KEY option with the new values
	 *
	 * @param mixed $new_values
	 *
	 * @return mixed
	 */
	public static function save_settings( $new_values ) {
		// Clear the cache so all new request have the updated values
		self::clear_cache();

		// Allow others to use our data
		do_action( 'zionbuilder/settings/save', $new_values );

		// Save the options to database
		return update_option( self::SETTINGS_OPTION_KEY, wp_json_encode( $new_values ) );
	}

	/**
	 * Get Allowed Post Types
	 *
	 * Will return all the post types for which the editor is allowed
	 *
	 * @return mixed|null
	 */
	public static function get_allowed_post_types() {
		return self::get_value( 'allowed_post_types' );
	}

	/**
	 * Get user role permissions settings
	 *
	 * Returns the user permissions from DB
	 *
	 * @return array
	 */
	public static function get_user_role_permissions_settings() {
		return self::get_value( 'user_roles_permissions' );
	}


	/**
	 * Get user permissions settings
	 *
	 * Returns saved permissions values for users
	 *
	 * @return array
	 */
	public static function get_users_permissions_settings() {
		return self::get_value( 'users_permissions' );
	}


	/**
	 * Get global colors
	 *
	 * Returns saved global colors
	 *
	 * @return array
	 */
	public static function get_global_colors() {
		return self::get_value( 'global_colors' );
	}
}
