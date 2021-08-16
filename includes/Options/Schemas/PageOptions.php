<?php

namespace ZionBuilder\Options\Schemas;

use ZionBuilder\Options\Options;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PageOptions
 *
 * @package ZionBuilder\Options\Schemas
 */
class PageOptions extends BaseSchema {
	/**
	 * @param string $post_type
	 *
	 * @return array
	 */
	public static function get_schema( $post_type ) {
		$page_options = new Options( 'zionbuilder/schema/page_options' );

		// Page templates
		$page_template_option = [
			[
				'id'   => 'default',
				'name' => esc_html__( 'Default', 'zionbuilder' ),
			],
		];

		$templates = get_page_templates( null, $post_type );
		ksort( $templates );
		foreach ( $templates as $name => $id ) {
			$page_template_option[] = [
				'id'   => $id,
				'name' => $name,
			];
		}

		$page_options_tab = $page_options->add_group(
			'page-options-group',
			[
				'title' => esc_html__( 'Page options', 'zionbuilder' ),
				'type'  => 'accordion_menu',
			]
		);

		if ( post_type_supports( $post_type, 'title' ) ) {
			$page_options_tab->add_option(
				'post_title',
				[
					'type'        => 'text',
					'description' => esc_html__( 'Enter the desired page title', 'zionbuilder' ),
					'title'       => esc_html__( 'Page title', 'zionbuilder' ),
					'placeholder' => esc_html__( 'Enter page title', 'zionbuilder' ),
				]
			);
		}

		$page_options_tab->add_option(
			'post_status',
			[
				'type'        => 'select',
				'description' => esc_html__( 'Select the page status.', 'zionbuilder' ),
				'title'       => esc_html__( 'Page status', 'zionbuilder' ),
				'default'     => 'publish',
				'options'     => [
					[
						'id'   => 'publish',
						'name' => esc_html__( 'Publish', 'zionbuilder' ),
					],
					[
						'id'   => 'draft',
						'name' => esc_html__( 'Draft', 'zionbuilder' ),
					],
					[
						'id'   => 'private',
						'name' => esc_html__( 'Private', 'zionbuilder' ),
					],
					[
						'id'   => 'pending',
						'name' => esc_html__( 'Pending Review', 'zionbuilder' ),
					],
				],
			]
		);

		$page_options_tab->add_option(
			'page_template',
			[
				'type'        => 'select',
				'description' => esc_html__( 'Select the desired page layout.', 'zionbuilder' ),
				'title'       => esc_html__( 'Page layout', 'zionbuilder' ),
				'default'     => 'default',
				'options'     => $page_template_option,
				'on_change'   => 'refresh_iframe',
			]
		);

		if ( post_type_supports( $post_type, 'thumbnail' ) ) {
			$page_options_tab->add_option(
				'page_thumbnail',
				[
					'type'        => 'image',
					'save-type'   => 'id',
					'description' => esc_html__( 'Choose the desired page featured image.', 'zionbuilder' ),
					'title'       => esc_html__( 'Page featured image', 'zionbuilder' ),
				]
			);
		}

		$custom_css_group = $page_options_tab->add_group(
			'custom-css-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Custom CSS', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		$custom_css_group->add_option(
			'_custom_css',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Custom CSS', 'zionbuilder' ),
				'message_description' => esc_html__( 'With custom CSS you can add CSS to your page.', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		$custom_js_group = $page_options_tab->add_group(
			'custom-js-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Custom JavaScript', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		$custom_js_group->add_option(
			'_custom_javascript',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Custom JavaScript', 'zionbuilder' ),
				'message_description' => esc_html__( 'With custom JavaScript you can add JavaScript code to this page.', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
			]
		);

		return $page_options->get_schema();
	}
}
