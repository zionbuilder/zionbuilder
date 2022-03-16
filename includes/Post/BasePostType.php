<?php

namespace ZionBuilder\Post;

use ZionBuilder\Nonces;
use ZionBuilder\Plugin;
use ZionBuilder\Permissions;
use ZionBuilder\Options\Schemas\PageOptions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class BasePostType
 *
 * @package ZionBuilder\Post
 */
class BasePostType {
	/**
	 * Holds a reference to the meta key where we store the page template data
	 */
	const PAGE_TEMPLATE_META_KEY = '_zionbuilder_page_elements';
	const PAGE_BUILDER_STATUS    = '_zionbuilder_page_status';

	/**
	 * Holds a reference to the \WP_Post instance
	 *
	 * @var \WP_Post
	 */
	private $post = null;

	/**
	 * Holds a reference to the \WP_Post instance
	 *
	 * @var null|int
	 */
	private $post_id = null;

	/**
	 * Holds a reference to the post page template data
	 *
	 * @var array
	 */
	private $post_elements_data = null;


	/**
	 * Holds a reference to the current post editor status
	 *
	 * @var boolean
	 */
	private $is_built_with_zion = null;

	/**
	 * Main class constructor
	 *
	 * @param \WP_Post|integer $post Post ID or post object
	 */
	public function __construct( $post ) {
		if ( is_numeric( $post ) ) {
			$this->post = get_post( $post );
		} elseif ( $post instanceof \WP_Post ) {
			$this->post = $post;
		}

		if ( null !== $this->post ) {
			$this->post_id = $this->post->ID;
		}
	}

	/**
	 * Get post
	 *
	 * Will return the WP_Post object
	 *
	 * @return null|\WP_Post
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Get Type
	 *
	 * Returns the post type name for which this class should apply
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'post';
	}

	/**
	 * Set Edit Mode
	 *
	 * Sets the editor status
	 *
	 * @param boolean $builder_status
	 *
	 * @return bool
	 */
	public function set_builder_status( $builder_status ) {
		if ( $builder_status ) {
			$status = update_post_meta( $this->get_post_id(), self::PAGE_BUILDER_STATUS, 'enabled' );
		} else {
			$status = delete_post_meta( $this->get_post_id(), self::PAGE_BUILDER_STATUS );
		}

		do_action( 'zionbuilder/post/set_builder_status', $builder_status, $this->get_post_id() );

		return $status;
	}

	/**
	 * Is Built With Zion
	 *
	 * Returns the page builder status for the current post
	 *
	 * @return boolean
	 */
	public function is_built_with_zion() {
		if ( null === $this->is_built_with_zion ) {
			$builder_status = get_post_meta( $this->get_post_id(), self::PAGE_BUILDER_STATUS, true );
			$status         = ( 'enabled' === $builder_status );

			$this->is_built_with_zion = apply_filters( 'zionbuilder/post/is_built_with_zion', $status, $this->get_post_id() );
		}

		return $this->is_built_with_zion;
	}


	/**
	 * Is password protected
	 *
	 * @return boolean Returns true/false in case the post is password protected
	 */
	public function is_password_protected() {
		return post_password_required( $this->get_post_id() );
	}

	/**
	 * Get Post Id
	 *
	 * Returns the current post id
	 *
	 * @return integer
	 */
	public function get_post_id() {
		return $this->post_id;
	}


	/**
	 * Returns a value from the cached WP_POST instance
	 *
	 * @param string $value_to_retrieve The post value that needs to be returned
	 *
	 * @return mixed
	 */
	public function get_post_value( $value_to_retrieve ) {
		if ( property_exists( $this->post, $value_to_retrieve ) ) {
			return $this->post->$value_to_retrieve;
		}

		return false;
	}

	/**
	 * Get Template Data
	 *
	 * Returns the saved pagebuilder data for the requested post
	 *
	 * @return array
	 */
	public function get_template_data() {
		$post_id = $this->get_post_id();

		// Check if we have a cached value
		if ( isset( $this->post_elements_data[$post_id] ) ) {
			return $this->post_elements_data[$post_id];
		}

		$template_data = get_post_meta( $post_id, self::PAGE_TEMPLATE_META_KEY, true );
		$template_data = json_decode( $template_data, true );
		$template_data = ! empty( $template_data ) ? $template_data : [];

		$this->post_elements_data[$post_id] = $template_data;

		return apply_filters( 'zionbuilder/post/get_template_data', $this->post_elements_data[$post_id], $post_id );
	}

	/**
	 * Get Post Template
	 *
	 * @return string
	 */
	public function get_post_template() {
		$saved_post_template = get_post_meta( $this->get_post_id(), '_wp_page_template', true );
		return apply_filters( 'zionbuilder/post/post_template', $saved_post_template, $this );
	}

	/**
	 * Get Template Data Version
	 *
	 * Returns the version of Zion Builder that was used to configure this page
	 *
	 * @return mixed
	 */
	public function get_template_data_version() {
		return get_post_meta( $this->get_post_id(), '_zionbuilder_version', true );
	}

	/**
	 * Get Edit Url.
	 *
	 * Will return the page edit url for the given post id
	 *
	 * @return string
	 */
	public function get_edit_url() {
		$url = add_query_arg(
			[
				'post_id' => absint( $this->get_post_id() ),
				'action'  => 'zion_builder_active',
			],
			admin_url( 'post.php' )
		);

		/*
		 * Page edit URL.
		 *
		 * Filters the Zion Builder edit url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url  Zion Builder edit url
		 */
		return apply_filters( 'zionbuilder/post/edit_url', $url );
	}

	/**
	 * Get Edit Url.
	 *
	 * Will return the page edit url for the given post id
	 *
	 * @return string
	 */
	public function get_preview_url_barebone() {
		$url = get_preview_post_link( $this->post_id, [ 'zionbuilder-barebone-preview' => true ] );

		/*
		 * Page preview URL.
		 *
		 * Filters the Zion Builder page preview url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url  Zion Builder edit url
		 */
		return apply_filters( 'zionbuilder/post/preview_url_barebone', set_url_scheme( $url ) );
	}

	/**
	 * Get Edit Url.
	 *
	 * Will return the page edit url for the given post id
	 *
	 * @return string
	 */
	public function get_preview_url() {
		$url = get_preview_post_link(
			$this->post_id,
			[
				'preview_id'    => $this->post_id,
				'preview_nonce' => wp_create_nonce( 'post_preview_' . $this->post_id ),
			]
		);

		/*
		 * Page preview URL.
		 *
		 * Filters the Zion Builder page preview url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url  Zion Builder edit url
		 */
		return apply_filters( 'zionbuilder/post/preview_url', set_url_scheme( $url ) );
	}




	/**
	 * Get All posts Url.
	 *
	 * Will return the page edit url for the given post id
	 *
	 * @return string
	 */
	public function get_all_pages_url() {
		$post_type = $this->get_post_value( 'post_type' );
		$url       = admin_url( 'edit.php?post_type=' . $post_type );

		/*
		 * Page preview URL.
		 *
		 * Filters the Zion Builder page preview url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url  Zion Builder edit url
		 */
		return apply_filters( 'zionbuilder/post/all_pages_url', $url );
	}

	/**
	 * Get Page Settings Schema
	 *
	 * Returns the list of options that will appear in the editor page settings pannel
	 *
	 * @return array List of options
	 */
	public function get_page_settings_schema() {
		$post_type = $this->get_post_value( 'post_type' );
		return PageOptions::get_schema( $post_type );
	}


	/**
	 * Returns the page values to be used in editor mode
	 *
	 * @return array
	 */
	public function get_page_settings_values() {
		$page_template = get_post_meta( $this->get_post_id(), '_wp_page_template', true );

		return apply_filters(
			'zionbuilder/post/page_options_data',
			[
				'post_title'     => $this->get_post_value( 'post_title' ),
				'post_status'    => $this->get_post_value( 'post_status' ),
				'page_template'  => $page_template ? $page_template : 'default',
				'page_thumbnail' => wp_get_attachment_url( get_post_thumbnail_id( $this->get_post_value( 'ID' ) ) ),
			],
			$this->get_post_id()
		);
	}

	/**
	 * Get Preview URL
	 *
	 * Will return the editor preview frame url
	 *
	 * @return string The preview frame url
	 */
	public function get_preview_frame_url() {
		$url = add_query_arg(
			[
				'zionbuilder-preview'  => $this->get_post_id(),
				Nonces::NONCE_FIELD_ID => Nonces::generate_nonce( Nonces::EDITOR_PREVIEW_FRAME ),
				'ver'                  => time(),
			],
			get_permalink( absint( $this->get_post_id() ) )
		);

		/*
		 * Preview Frame URL.
		 *
		 * Filters the Zion Builder preview url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url  Zion Builder page preview frame url
		 */
		return apply_filters( 'zionbuilder/post/preview_frame_url', set_url_scheme( $url ) );
	}

	/**
	 * Save template data
	 *
	 * Saves the pagebuilder elements data to post meta
	 *
	 * @param array $template_data
	 *
	 * @return void
	 */
	public function save_template_data( $template_data = [] ) {
		$post_id = $this->get_post_id();

		if ( empty( $template_data ) ) {
			delete_metadata( 'post', $post_id, self::PAGE_TEMPLATE_META_KEY );
		} else {
			// Use update_metadata as update_post_meta || meta_input for wp_insert_post always saves to main post instead of revision
			// Slash data
			// @see: https://wordpress.stackexchange.com/questions/53336/wordpress-is-stripping-escape-backslashes-from-json-strings-in-post-meta
			$pb_data = wp_slash( wp_json_encode( $template_data ) );
			update_metadata( 'post', $post_id, self::PAGE_TEMPLATE_META_KEY, $pb_data );
		}

		// Update cache
		$this->post_elements_data[$post_id] = $template_data;
	}

	/**
	 * Save
	 *
	 * Saves the current post to database
	 *
	 * @param array $post_data
	 *
	 * @return bool
	 */
	public function save( $post_data ) {
		// Don't proceed if the user is not allowed to edit this post
		if ( ! Permissions::can_edit_post( $this->get_post_id() ) ) {
			return false;
		}

		$current_post_status = $this->get_post_value( 'post_status' );
		$status              = isset( $post_data['status'] ) ? $post_data['status'] : $current_post_status;
		$is_autosave         = 'autosave' === $status;

		if ( $is_autosave && in_array( $current_post_status, [ 'publish', 'private' ], true ) ) {
			$this->save_autosave( $post_data );
		} else {
			if ( 'publish' === $status ) {
				$post_data['page_settings']['post_status'] = 'publish';
			}
			$this->save_current_post( $post_data );
		}
		return true;
	}

	/**
	 * Returns the post id if the current post is and autosave or false
	 *
	 * @return int|false
	 */
	public function is_autosave() {
		return wp_is_post_autosave( $this->get_post_id() );
	}

	/**
	 * Save current post
	 *
	 * Saves the current post
	 *
	 * @param array $post_data
	 *
	 * @return \WP_Error|int
	 */
	public function save_current_post( $post_data = [] ) {
		$post_id       = $this->get_post_id();
		$page_settings = [];

		if ( ! $post_id ) {
			return new \WP_Error( 'post_id_not_found', 'Post with id cannot be found', 'zionbuilder' );
		}

		$is_autosave = $this->is_autosave();

		if ( $is_autosave ) {
			$post_data['page_settings']['post_status'] = 'inherit';
		} elseif ( $post_data['page_settings']['post_status'] === 'inherit' ) {
			// If this is not an autosave, we need to set the proper status for the post
			$post_data['page_settings']['post_status'] = get_post_status( $post_id );
		}

		// hold the new post data
		$new_post_data = [
			'ID' => $post_id,
		];

		// Save the post template data
		if ( isset( $post_data['template_data'] ) ) {
			$this->save_template_data( $post_data['template_data'] );
		}

		// Save the builder status
		if ( isset( $post_data['builder_status'] ) ) {
			$this->set_builder_status( $post_data['builder_status'] );
		}

		// Page settings
		if ( ! empty( $post_data['page_settings'] ) ) {
			$page_settings = $post_data['page_settings'];

			foreach ( [
				'post_title',
				'post_status',
			] as $option_id ) {
				if ( isset( $page_settings[$option_id] ) ) {
					$new_post_data[$option_id] = $page_settings[$option_id];
				}
			}

			// Page template
			if ( isset( $page_settings['page_template'] ) ) {
				update_metadata( 'post', $post_id, '_wp_page_template', $page_settings['page_template'] );
			}

			// Implement options changes so we don't update the posts each time
			// Post thumbnail
			if ( isset( $page_settings['page_thumbnail'] ) ) {
				$attachment_id                  = $page_settings['page_thumbnail'] ? attachment_url_to_postid( $page_settings['page_thumbnail'] ) : -1;
				$new_post_data['_thumbnail_id'] = $attachment_id;
			}
		}

		do_action( 'zionbuilder/post/save', $new_post_data, $page_settings, $post_id );

		// update post new values, will return a WP_ERROR on fail
		$post_id = wp_update_post( wp_slash( $new_post_data ), true );

		return $post_id;
	}

	/**
	 * @param array $post_data
	 *
	 * @return \WP_Error|bool
	 */
	protected function save_autosave( $post_data = [] ) {
		$autosave = $this->get_autosave();

		// WP autosaves have the inherit post status
		// This is mandatory for the autosave system to work
		$post_data['page_settings']['post_status'] = 'inherit';
		$autosave->save_current_post( $post_data );
		return true;
	}


	/**
	 * Returns the autosave for the current post
	 *
	 * @return BasePostType
	 */
	protected function get_autosave() {
		$current_user_id = get_current_user_id();
		$post_autosave   = wp_get_post_autosave( $this->get_post_id(), $current_user_id );

		// If the autosave doesn't exist, create one
		if ( $post_autosave instanceof \WP_Post ) {
			$post_autosave_id = $post_autosave->ID;
		} else {
			if ( ! function_exists( 'wp_create_post_autosave' ) ) {
				require_once ABSPATH . 'wp-admin/includes/post.php';
			}

			$post_autosave_id = wp_create_post_autosave(
				[
					'post_ID'       => $this->get_post_id(),
					'post_type'     => $this->get_post_value( 'post_type' ),
					'post_title'    => $this->get_post_value( 'post_title' ),
					'content'       => $this->get_post_value( 'post_content' ),
					'post_excerpt'  => $this->get_post_value( 'post_excerpt' ),
					'post_modified' => current_time( 'mysql' ),
				]
			);

			// Copy pb data to autosave
			$this->copy_meta_fields( $post_autosave_id );
		}

		return Plugin::$instance->post_manager->get_post_instance( $post_autosave_id );
	}

	/**
	 * Returns data that will be used from REST API
	 *
	 * @return array
	 */
	public function get_data_for_api() {
		return [];
	}

	/**
	 * Copies the values from the current post id to the given post id
	 *
	 * @param integer $to_post_id The post id to which we need to copy the values
	 *
	 * @return void
	 */
	public function copy_meta_fields( $to_post_id ) {
		$from_post_id = $this->get_post_id();

		// Allow others to add their own options to pagebuilder frontend and save them
		$meta_fields = apply_filters(
			'zionbuilder/post/meta_fields_for_copy',
			[
				self::PAGE_TEMPLATE_META_KEY,
				self::PAGE_BUILDER_STATUS,
				// WP specific
				'_wp_page_template',
				'_thumbnail_id',
			]
		);

		$post_meta = get_post_meta( $from_post_id );
		foreach ( $post_meta as $meta_key => $meta_value ) {
			$value = $meta_value[0];

			if ( in_array( $meta_key, $meta_fields, true ) ) {
				if ( self::PAGE_TEMPLATE_META_KEY === $meta_key ) {
					// slash page builder data
					$value = wp_slash( $meta_value[0] );
				}

				update_metadata( 'post', $to_post_id, $meta_key, $value );
			}
		}
	}
}
