<?php

namespace ZionBuilder\Elements\Gallery;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;
use ZionBuilder\WPMedia;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class Gallery extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'gallery';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Gallery', 'zionbuilder' );
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
		return [ 'image', 'gallery', 'showcase', 'portfolio', 'filterable', 'image', 'photo', 'photography', 'picture' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-gallery';
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
			'columns',
			[
				'type'               => 'column_size',
				'title'              => esc_html__( 'Columns', 'zionbuilder' ),
				'default'            => 3,
				'options'            => [
					[
						'name' => '1',
						'id'   => 1,
					],
					[
						'name' => '2',
						'id'   => 2,
					],
					[
						'name' => '3',
						'id'   => 3,
					],
					[
						'name' => '4',
						'id'   => 4,
					],
					[
						'name' => '5',
						'id'   => 5,
					],
				],
				'responsive_options' => true,
				'render_attribute'   => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-el-gallery-columns{{RESPONSIVE_DEVICE_CSS}}--{{VALUE}}',
					],
				],
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
		//              'tag_id'    => 'image-wrappers',
		//              'attribute' => 'data-zion-lightbox',
		//              'value'     => true,
		//          ],
		//      ],
		//  ]
		// );

		$options->add_option(
			'images',
			[
				'type'    => 'image_gallery',
				'title'   => esc_html__( 'Gallery', 'zionbuilder' ),
				'default' => [
					[
						'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
					],
					[
						'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
					],
					[
						'image' => Utils::get_file_url( 'assets/img/no-image.jpg' ),
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
			'image_wrapper_styles',
			[
				'title'    => esc_html__( 'Gallery item styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-gallery-item',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Gallery/editor', 'js' ) );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Gallery/frontend.css' ) );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		$use_modal = $options->get_value( 'use_modal' );

		if ( $use_modal ) {
			$this->render_attributes->add(
				'wrapper',
				'data-zion-lightbox',
				wp_json_encode(
					[
						'selector' => '',
					]
				)
			);
			$this->render_attributes->add( 'wrapper', 'class', 'zb-el-gallery--has-modal' );
		}
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$images    = $options->get_value( 'images' );
		$use_modal = $options->get_value( 'use_modal' );

		if ( empty( $images ) || ! is_array( $images ) ) {
			esc_html_e( 'No images selected.', 'zionbuilder' );
			return;
		}

		foreach ( $images as $image ) {
			if ( ! empty( $image['image'] ) ) {
				$attributes               = $use_modal ? 'data-src="' . esc_attr( $image['image'] ) . '"' : $this->render_attributes->get_attributes_as_string( 'image_wrapper_styles', [] );
				$image_custom_css_classes = $this->get_style_classes_as_string( 'image_wrapper_styles', [ 'zb-el-gallery-item' ] );

				$image = WPMedia::get_imge(
					$image['image'],
					[]
				);

				// Disabled check as the attributes are already escaped
				printf( '<div class="%s" %s>%s</div>', esc_attr( $image_custom_css_classes ), $attributes, $image ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
	}
}
