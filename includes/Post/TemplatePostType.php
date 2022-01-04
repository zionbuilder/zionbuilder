<?php

namespace ZionBuilder\Post;

use ZionBuilder\Post\BasePostType;
use ZionBuilder\Templates;

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
				'thumbnail'                 => get_the_post_thumbnail_url( $this->get_post() ),
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
}
