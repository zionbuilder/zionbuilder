<?php

namespace ZionBuilder\Post;

use ZionBuilder\Post\BasePostType;
use ZionBuilder\Templates;
use ZionBuilder\FileSystem;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TemplatePostType
 *
 * @package ZionBuilder\Post
 */
class TemplatePostType extends BasePostType {
	const THUMBNAIL_META_FIELD        = 'zionbuilder_template_thumbnail';
	const THUMBNAIL_FAILED_META_FIELD = 'zionbuilder_template_thumbnail_failed';

	const CACHE_DIRECTORY_NAME = 'screenshots';

	/**
	 * Get Type
	 *
	 * Returns the post type name for which this class should apply
	 *
	 * @return string
	 */
	public static function get_type() {
		return Templates::TEMPLATE_POST_TYPE;
	}


	/**
	 * Returns the screenshot generation URL string
	 *
	 * @return string|null
	 */
	public function get_screenshot_generation_url() {
		return get_preview_post_link(
			$this->get_post_id(),
			[
				'zionbuilder-barebone-preview'    => true,
				'zionbuilder-generate-screenshot' => true,
			]
		);
	}

	/**
	 * Returns data that will be used from REST API
	 *
	 * @return array
	 */
	public function get_data_for_api() {
		$filter_hook     = sprintf( 'zionbuilder/post/%s/data_for_api', Templates::TEMPLATE_POST_TYPE );
		$item_categories = [ get_post_meta( $this->get_post_id(), Templates::TEMPLATE_TYPE_META, true ) ];

		// Template author
		$author    = '';
		$user_data = get_user_by( 'id', $this->get_post_value( 'post_author' ) );
		if ( $user_data ) {
			$author = $user_data->display_name;
		}

		// Shortcode
		$prefix_shortcode = 'zionbuilder';
		$shortcode        = '[' . $prefix_shortcode . ' id="' . $this->get_post_id() . '"]';

		return apply_filters(
			$filter_hook,
			[
				'id'               => $this->get_post_id(),
				'name'             => $this->get_post_value( 'post_title' ),
				'status'           => $this->get_post_value( 'post_status' ),
				'author'           => $author,
				'shortcode'        => $shortcode,
				'category'         => $item_categories,
				'thumbnail'        => $this->get_thumbnail(),
				'thumbnail_failed' => $this->has_thumbnail_generation_failed(),
				'date'             => $this->get_post_value( 'post_date' ),
				'tags'             => [],
				'urls'             => [
					'edit_url'                  => $this->get_edit_url(),
					'preview_url'               => $this->get_preview_url_barebone(),
					'screenshot_generation_url' => $this->get_screenshot_generation_url(),
				],
				'type'             => get_post_meta( $this->get_post_id(), Templates::TEMPLATE_TYPE_META, true ),
			],
			$this->get_post()
		);
	}

	/**
	 * Returns the current status of screenshot generation process
	 *
	 * @return boolean
	 */
	public function has_thumbnail_generation_failed() {
		return get_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD, true ) === true;
	}

	/**
	 * Returns the template screenshot
	 *
	 * @return string
	 */
	public function get_thumbnail() {
		return get_post_meta( $this->get_post_id(), self::THUMBNAIL_META_FIELD, true );
	}

	/**
	 * Saves the thumbnail image to DB
	 *
	 * @param string $thumbnail_url
	 *
	 * @return void
	 */
	public function set_thumbnail( $thumbnail_url ) {
		update_post_meta( $this->get_post_id(), self::THUMBNAIL_META_FIELD, $thumbnail_url );
		delete_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD );
	}

	/**
	 * Saves the screenshot generation status to DB
	 *
	 * @param boolean $status
	 *
	 * @return void
	 */
	public function set_failed_thumbnail_generation_status( $status ) {
		update_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD, $status );
	}

	/**
	 * Deletes the thumbnail data from DB and file system
	 *
	 * @return void
	 */
	public function clear_thumbnail_data() {
		delete_post_meta( $this->get_post_id(), self::THUMBNAIL_META_FIELD );
		delete_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD );

		$screenshot_folder = FileSystem::get_zionbuilder_upload_dir( self::CACHE_DIRECTORY_NAME );
		$file_path         = sprintf( '%s/template-%s.png', $screenshot_folder['basedir'], $this->get_post_id() );

		FileSystem::get_file_system()->delete( $file_path );
	}

	/**
	 * Saves a base64 image data as template thumbnail
	 *
	 * @param string $base_64_data
	 *
	 * @return void
	 */
	public function save_base64Image( $base_64_data ) {
		// Get the base64 string
		$image_data = str_replace( 'data:image/png;base64,', '', $base_64_data );
		// phpcs:ignore
		$image_data = base64_decode( $image_data );

		$screenshot_folder = FileSystem::get_zionbuilder_upload_dir( self::CACHE_DIRECTORY_NAME );
		$file_path         = sprintf( '%s/template-%s.png', $screenshot_folder['basedir'], $this->get_post_id() );
		$file_url          = sprintf( '%s/template-%s.png', $screenshot_folder['baseurl'], $this->get_post_id() );

		FileSystem::get_file_system()->put_contents( $file_path, $image_data, 0644 );

		// TODO: check for fail
		$this->set_thumbnail( $file_url );
	}

	/**
	 * Saves the template data to DB
	 *
	 * @param array $post_data
	 *
	 * @return boolean
	 */
	public function save( $post_data ) {
		// Clear the thumbnail data so we can regenerate it
		$this->clear_thumbnail_data();

		return parent::save( $post_data );
	}
}
