<?php

namespace ZionBuilder\Post;

use ZionBuilder\Post\BasePostType;
use ZionBuilder\Post\TemplatePostType;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PostManager
 *
 * @package ZionBuilder\Post
 */
class PostManager {
	/**
	 * Holds the list of all registered post types
	 *
	 * @var array
	 */
	private $registered_post_types = [];

	/**
	 * Loaded Posts
	 *
	 * Holds a reference to all posts loaded trough the PostManager
	 *
	 * @var array
	 */
	private $loaded_posts = [];

	/**
	 * Holds a reference to the active post
	 *
	 * @var integer
	 */
	private $active_post_id = null;

	/**
	 * Holds a reference to the previously active post
	 *
	 * @var integer
	 */
	private $old_post_id = null;

	/**
	 * Register Post Types
	 *
	 * Fires an action that plugin or theme authors can use to add their own post type custom functionality
	 *
	 * @return void
	 */
	public function register_post_types() {
		$this->register_post_type( 'ZionBuilder\Post\TemplatePostType' );

		/*
		 * Allow others to register their own post types configs
		 */
		do_action( 'zionbuilder/post_manager/register_post_type', $this );
	}

	/**
	 * Register Post Type
	 *
	 * Will register a custom handler of a post type
	 *
	 * @param string $type_class_name The PHP class name that will be registered
	 *
	 * @return void
	 */
	public function register_post_type( $type_class_name ) {
		$name = $type_class_name::get_type();
		if ( empty( $name ) ) {
			return;
		}
		$this->registered_post_types[$name] = $type_class_name;
	}

	/**
	 * Unregister Post Type
	 *
	 * @param string $type_class_name The PHP class name that will be unregistered
	 *
	 * @return void
	 */
	public function unregister_post_type( $type_class_name ) {
		$name = $type_class_name::get_type();
		if ( empty( $name ) ) {
			return;
		}
		unset( $this->registered_post_types[$name] );
	}

	/**
	 * Switch To Post
	 *
	 * Will modify the global post object to a different post
	 *
	 * @param integer $post_id
	 *
	 * @return PostManager
	 */
	public function switch_to_post( $post_id ) {
		// Proceed if this is the active post id
		if ( $post_id === $this->active_post_id ) {
			return $this;
		}

		$this->old_post_id    = $this->active_post_id;
		$this->active_post_id = absint( $post_id );

		// Set the global post
		$GLOBALS['post'] = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $GLOBALS['post'] );

		// Allow chaining
		return $this;
	}

	/**
	 * Set previous post as active
	 *
	 * @return void
	 */
	public function reset_post() {
		$this->switch_to_post( $this->old_post_id );
	}

	/**
	 * Get Active Post ID
	 *
	 * Returns the post id that is currently active
	 *
	 * @return integer Post id that is active
	 */
	public function get_active_post_id() {
		if ( $this->active_post_id ) {
			return $this->active_post_id;
		}

		// Set the post id for singular pages
		if ( is_singular() ) {
			$this->active_post_id = apply_filters( 'zionbuilder/post_manager/post_id', get_the_ID() );
		}

		return $this->active_post_id;
	}

	/**
	 * Get Active Post Instance
	 *
	 * Returns an instance of the active post instance data
	 *
	 * @return BasePostType|false
	 */
	public function get_active_post_instance() {
		// Get the active post id
		$post_id = $this->get_active_post_id();

		// Return post if it was already loaded
		if ( null !== $post_id && isset( $this->loaded_posts[$post_id] ) ) {
			return $this->loaded_posts[$post_id];
		}

		return $this->get_post_instance( $post_id );
	}

	/**
	 * Get Post Instance for render
	 *
	 * Returns the active post instance used for rendering the page.
	 * If the page is a preview, it will load the autosave for that page
	 *
	 * @return BasePostType|false
	 */
	public function get_active_post_instance_for_render() {
		$post_id = $this->get_active_post_id();

		// If it is WordPress preview, check to see if we have an autosave
		if ( is_preview() ) {
			$post_instance = $this->get_post_or_autosave_instance( $post_id );
		} else {
			$post_instance = $this->get_post_instance( $post_id );
		}

		return $post_instance;
	}

	/**
	 * Get Post Instance
	 *
	 * Returns an instance of the requested post
	 *
	 * @param integer $post_id Post id for which the BasePostType instance should be returned
	 *
	 * @return BasePostType|false|TemplatePostType
	 */
	public function get_post_instance( $post_id ) {
		// Only register post types if not already registered
		if ( empty( $this->registered_post_types ) ) {
			$this->register_post_types();
		}

		// Check to see if the post was saved
		if ( isset( $this->loaded_posts[$post_id] ) ) {
			return $this->loaded_posts[$post_id];
		}

		// Create instance of post
		$post = get_post( $post_id );

		if ( null === $post ) {
			return false;
		}

		// Get an instance of the post type
		$this->loaded_posts[$post_id] = $this->get_post_type_instance( $post );

		// Return the instance of post type
		return $this->loaded_posts[$post_id];
	}

	/**
	 * Get post or autosave
	 *
	 * If the post has an autosave, it will return it. If not, it will return the main post instance
	 *
	 * @param mixed $post_id
	 *
	 * @return bool|BasePostType|TemplatePostType
	 */
	public function get_post_or_autosave_instance( $post_id ) {
		$user_id               = get_current_user_id();
		$autosave              = wp_get_post_autosave( $post_id, $user_id );
		$current_post_instance = $this->get_post_instance( $post_id );

		// Check if the autosave is newer
		if ( false !== $autosave ) {
			// Check to see if the autosave is newer
			$autosave_post_modified = mysql2date( 'U', $autosave->post_modified_gmt, false );
			$current_post_modified  = mysql2date( 'U', $current_post_instance->get_post_value( 'post_modified_gmt' ), false );

			if ( $autosave_post_modified > $current_post_modified ) {
				return $this->get_post_instance( $autosave->ID );
			}
		}

		return $this->get_post_instance( $post_id );
	}

	/**
	 * Get Post Type Instance
	 *
	 * Returns an instance of the post type based on post data
	 *
	 * @param \WP_Post|int $post
	 *
	 * @return BasePostType|TemplatePostType
	 */
	public function get_post_type_instance( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post instanceof \WP_Post ) {
			return new BasePostType( 0 );
		}

		$post_type = $post->post_type;

		if ( isset( $this->registered_post_types[$post_type] ) ) {
			$post_type_class_name = $this->registered_post_types[$post_type];
			return new $post_type_class_name( $post );
		}

		return new BasePostType( $post );
	}
}
