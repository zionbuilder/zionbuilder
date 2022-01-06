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


	public function get_screenshot_generation_url() {
		return get_preview_post_link(
			$this->get_post_id(),
			[
				'zionbuilder-barebone-preview'    => true,
				'zionbuilder-generate-screenshot' => true,
			]
		);
	}

	public function get_data_for_api() {
		$filter_hook     = sprintf( 'zionbuilder/post/%s/data_for_api', Templates::TEMPLATE_POST_TYPE );
		$item_categories = [ get_post_meta( $this->get_post_id(), Templates::TEMPLATE_TYPE_META, true ) ];

		return apply_filters(
			$filter_hook,
			[
				'id'                        => $this->get_post_id(),
				'name'                      => $this->get_post_value( 'post_title' ),
				'category'                  => $item_categories,
				'thumbnail'                 => $this->get_thumbnail(),
				'thumbnail_failed'          => $this->has_thumbnail_generation_failed(),
				'date'                      => $this->get_post_value( 'post_date' ),
				'tags'                      => [],
				'edit_url'                  => $this->get_edit_url(),
				'preview_url'               => $this->get_preview_url_barebone(),
				'screenshot_generation_url' => $this->get_screenshot_generation_url(),
				'type'                      => get_post_meta( $this->get_post_id(), Templates::TEMPLATE_TYPE_META, true ),
				'source'                    => 'local',
				'url'                       => '',
				'pro'                       => false,
			],
			$this->get_post()
		);
	}

	public function has_thumbnail_generation_failed() {
		return get_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD, true ) === true;
	}

	public function get_thumbnail() {
		return get_post_meta( $this->get_post_id(), self::THUMBNAIL_META_FIELD, true );
	}

	public function set_thumbnail( $thumbnail_url ) {
		update_post_meta( $this->get_post_id(), self::THUMBNAIL_META_FIELD, $thumbnail_url );
		delete_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD );
	}

	public function set_failed_thumbnail_generation() {
		update_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD, true );
	}

	public function clear_thumbnail_data() {
		delete_post_meta( $this->get_post_id(), self::THUMBNAIL_META_FIELD );
		delete_post_meta( $this->get_post_id(), self::THUMBNAIL_FAILED_META_FIELD );
	}

	public function save_base64Image( $base_64_data ) {
		// Get the base64 string
		$image_data = str_replace( 'data:image/png;base64', '', $base_64_data );

		$screenshoot_folder = FileSystem::get_zionbuilder_upload_dir( self::CACHE_DIRECTORY_NAME );
		$file_path          = sprintf( '%s/template-%s.png', $screenshoot_folder['basedir'], $this->get_post_id() );
		$file_url           = sprintf( '%s/template-%s.png', $screenshoot_folder['baseurl'], $this->get_post_id() );

		FileSystem::get_file_system()->put_contents( $file_path, $image_data, 0644 );

		// TODO: check for fail
		$this->set_thumbnail( $file_url );
	}
}
