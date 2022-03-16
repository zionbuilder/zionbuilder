<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Permissions
 *
 * @package ZionBuilder
 */
class Permissions {
	const POST_TYPE_EDIT_PERMISSION    = 'zionbuilder';
	const USER_EDIT_PERMISSION         = 'zionbuilder';
	const USER_CONTENT_ONLY_PERMISSION = 'zionbuilder-content';


	/**
	 * Holds a cached value as true/false if the current user can edit the page with Zion builder
	 *
	 * @var boolean
	 */
	private static $current_user_allowed_edit = null;

	/**
	 * Main class constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_post_type_support' ] );
	}


	/**
	 * Returns a list of post types for which the current user has permissions to edit
	 *
	 * @return array
	 */
	public static function get_allowed_post_types() {
		return apply_filters( 'zionbuilder/permissions/get_allowed_post_types', Settings::get_allowed_post_types() );
	}

	/**
	 * Add Post Type Support
	 *
	 * Adds zion builder edit support for allowed post types
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_post_type_support() {
		$allowed_post_types = self::get_allowed_post_types();

		if ( ! is_array( $allowed_post_types ) ) {
			return;
		}

		// Add zion builder capability to post types
		foreach ( $allowed_post_types as $post_type ) {
			add_post_type_support( $post_type, self::POST_TYPE_EDIT_PERMISSION );
		}
	}


	/**
	 * Allowed Post Type
	 *
	 * Check if the current post type should have the page builder activated or not
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type The post type we should check the permission
	 *
	 * @return bool
	 */
	public static function allowed_post_type( $post_type ) {
		$can_edit_post_type = post_type_supports( $post_type, self::POST_TYPE_EDIT_PERMISSION );

		/*
		 * Allow theme and plugins to disable editing functionality for post type
		 */
		return apply_filters( 'zionbuilder/permissions/allowed_post_type', $can_edit_post_type, $post_type );
	}


	/**
	 * Returns true/false if the user can edit the current post
	 *
	 * @return boolean
	 */
	public static function user_allowed_edit() {
		$current_user_can_edit = false;

		// Administrators are allowed to edit
		if ( current_user_can( 'administrator' ) ) {
			return true;
		}

		// Cache the returned value
		if ( null !== self::$current_user_allowed_edit ) {
			return self::$current_user_allowed_edit;
		}

		$permissions  = Settings::get_user_role_permissions_settings();
		$current_user = wp_get_current_user();
		$user_roles   = (array) $current_user->roles;

		// Check if the user has access to the page builder
		foreach ( $user_roles as $role_id ) {
			if ( isset( $permissions[$role_id]['allowed_access'] ) && $permissions[$role_id]['allowed_access'] ) {
				$current_user_can_edit = true;
				break;
			}
		}

		/*
		 * Allow theme and plugins to disable editing functionality for users
		 */
		return apply_filters( 'zionbuilder/permissions/allowed_current_user', $current_user_can_edit );
	}


	/**
	 * Check Edit Post
	 *
	 * Checks if the user can edit the post for the given post id
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 *
	 * @return boolean
	 */
	public static function can_edit_post( $post_id ) {
		$post_id   = absint( $post_id );
		$post_type = get_post_type( $post_id );

		if ( false === $post_type ) {
			return false;
		}

		// Check permissions
		$can_edit_post_type = self::allowed_post_type( $post_type );
		$user_can_edit      = self::user_allowed_edit();

		$can_edit = $can_edit_post_type && $user_can_edit;

		return apply_filters( 'zionbuilder/permissions/allow_edit', $can_edit, $post_id, $post_type );
	}


	/**
	 * Checks if the current user can perform an action
	 *
	 * @param string $permission
	 *
	 * @return boolean
	 */
	public static function current_user_can( $permission ) {
		$user_can = false;

		switch ( $permission ) {
			default:
				$user_can = self::user_allowed_edit();
				break;
		}

		$hook = sprintf( 'zionbuilder/permissions/user_allowed/%s', $permission );
		return apply_filters( $hook, $user_can );
	}
}
