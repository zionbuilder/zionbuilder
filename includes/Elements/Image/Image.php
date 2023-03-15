<?php

namespace ZionBuilder\Elements\Image;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;
use ZionBuilder\WPMedia;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Image
 *
 * @package ZionBuilder\Elements
 */
class Image extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_image';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Image', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'media';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'image', 'media', 'picture', 'photo' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-image';
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'image',
			[
				'type'        => 'image',
				'id'          => 'image',
				'description' => 'Choose the desired image.',
				'title'       => esc_html__( 'Image', 'zionbuilder' ),
				'show_size'   => true,
				'default'     => [
					'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
				],
			]
		);

		$options->add_option(
			'align',
			[
				'type'                    => 'text_align',
				'title'                   => esc_html__( 'Align', 'zionbuilder' ),
				'description'             => esc_html__( 'Select the desired alignment.', 'zionbuilder' ),
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.text-align',
				'show_responsive_buttons' => true,
			]
		);

		$options->add_option(
			'link',
			[
				'type'        => 'link',
				'description' => 'This is the element content',
				'title'       => esc_html__( 'Link', 'zionbuilder' ),
			]
		);

		// $options->add_option(
		//  'use_modal',
		//  [
		//      'type'             => 'checkbox_switch',
		//      'default'          => false,
		//      'layout'           => 'inline',
		//      'title'            => esc_html__( 'Use modal', 'zionbuilder' ),
		//      'render_attribute' => [
		//          [
		//              'tag_id'    => 'link',
		//              'attribute' => 'data-zion-lightbox',
		//              'value'     => true,
		//          ],
		//      ],
		//  ]
		// );

		$options->add_option(
			'show_caption',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Show caption', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'caption_text',
			[
				'type'       => 'textarea',
				'title'      => esc_html__( 'Caption Text', 'zionbuilder' ),
				'default'    => esc_html__( 'Image caption.', 'zionbuilder' ),
				'dependency' => [
					[
						'option' => 'show_caption',
						'value'  => [ true ],
					],
				],
			]
		);
	}

	/**
	 * Get style elements
	 *
	 * Returns a list of elements/tags that for which you
	 * want to show style options
	 *
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'link_styles',
			[
				'title'      => esc_html__( 'Link styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} a',
				'render_tag' => 'link_styles',
			]
		);
		$this->register_style_options_element(
			'image_styles',
			[
				'title'      => esc_html__( 'Image styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} img',
				'render_tag' => 'image_styles',
			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Image/editor', 'js' ) );
	}

	/**
	 * Enqueue element styles for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_style(), enqueue_element_style() functions
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		// Using helper methods will go through caching policy
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Image/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		// Get attachment id
		$link         = $options->get_value( 'link', false );
		$use_modal    = $options->get_value( 'use_modal', false );
		$show_caption = $options->get_value( 'show_caption', false );
		$caption_text = $options->get_value( 'caption_text', false );
		$image_value  = $options->get_value(
			'image',
			[
				'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
			]
		);

		// Don't proceed if we don't have an image
		if ( empty( $image_value['image'] ) ) {
			return;
		}
		$html_tag = 'div';

		$combined_image_attr = $this->render_attributes->get_combined_attributes_as_key_value( 'image_styles', [] );

		$image = WPMedia::get_imge(
			$image_value,
			$combined_image_attr
		);

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'link_styles', $link );
			$html_tag = 'a';
		}

		if ( $use_modal ) {
			$this->render_attributes->add( 'link_styles', 'href', 'https://vimeo.com/357762214', true );
			$html_tag = 'a';
		}

		$this->render_tag( $html_tag, 'link_styles', $image );

		if ( $show_caption && $caption_text ) {
			$this->render_tag(
				'div',
				'caption',
				$caption_text,
				[
					'class' => 'zb-el-zionImage-caption',
				]
			);
		}
	}
}
