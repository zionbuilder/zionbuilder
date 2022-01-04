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
			$this->post_id,
			[
				'zionbuilder-barebone-preview'    => true,
				'zionbuilder-generate-screenshot' => true,
			]
		);
	}
}
