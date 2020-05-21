<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Image
 *
 * @package ZionBuilder\Elements
 */
class ImageBox extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'image_box';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Image Box', 'zionbuilder' );
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
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'image', 'media', 'box' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-image-box';
	}

	/**
	 * Registers the element options
	 *
	 * @param Options $options The Options instance
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
			'position',
			[
				'type'             => 'select',
				'default'          => 'row',
				'title'            => esc_html__( 'Image position', 'zionbuilder' ),
				'description'      => esc_html__( 'Set the desired timing function for the transition.', 'zionbuilder' ),
				'options'          => [
					[
						'name' => esc_html__( 'Image on top', 'zionbuilder' ),
						'id'   => 'column',
					],
					[
						'name' => esc_html__( 'Image on left', 'zionbuilder' ),
						'id'   => 'row',
					],
					[
						'name' => esc_html__( 'Image on right', 'zionbuilder' ),
						'id'   => 'row-reverse',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-flex--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'align',
			[
				'type'             => 'text_align',
				'title'            => __( 'Align', 'zionbuilder' ),
				'description'      => __( 'Select the desired alignment.', 'zionbuilder' ),
				'dependency'       => [
					[
						'option' => 'position',
						'value'  => [ 'column' ],
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb__utils-t-align--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'title',
			[
				'type'        => 'text',
				'description' => esc_html__( 'Set the desired title.', 'zionbuilder' ),
				'title'       => esc_html__( 'Image box title', 'zionbuilder' ),
				'default'     => esc_html__( 'Just a sample title.', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'description',
			[
				'type'    => 'editor',
				'title'   => esc_html__( 'Description text', 'zionbuilder' ),
				'default' => sprintf( '<p>%s</p>', __( 'Just a sample text from heading element.', 'zionbuilder' ) ),
			]
		);

		$options->add_option(
			'title_tag',
			[
				'type'        => 'select',
				'default'     => 'h3',
				'title'       => __( 'HTML tag', 'zionbuilder' ),
				'description' => __( 'Set the desired heading tag.', 'zionbuilder' ),
				'options'     => [
					[
						'name' => __( 'h1', 'zionbuilder' ),
						'id'   => 'h1',
					],
					[
						'name' => __( 'h2', 'zionbuilder' ),
						'id'   => 'h2',
					],
					[
						'name' => __( 'h3', 'zionbuilder' ),
						'id'   => 'h3',
					],
					[
						'name' => __( 'h3', 'zionbuilder' ),
						'id'   => 'h3',
					],
					[
						'name' => __( 'h5', 'zionbuilder' ),
						'id'   => 'h5',
					],
					[
						'name' => __( 'h6', 'zionbuilder' ),
						'id'   => 'h6',
					],
					[
						'name' => __( 'p', 'zionbuilder' ),
						'id'   => 'p',
					],
					[
						'name' => __( 'span', 'zionbuilder' ),
						'id'   => 'span',
					],
					[
						'name' => __( 'div', 'zionbuilder' ),
						'id'   => 'div',
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
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'image_styles',
			[
				'title'    => esc_html__( 'Image styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-imageBox-image',
			]
		);
		$this->register_style_options_element(
			'title_styles',
			[
				'title'      => esc_html__( 'Title styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-imageBox-title',
				'render_tag' => 'title',
			]
		);
		$this->register_style_options_element(
			'description_styles',
			[
				'title'      => esc_html__( 'Description styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-imageBox-description',
				'render_tag' => 'description',
			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_script(), enqueue_element_script() functions
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/ImageBox/editor.js' ) );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/ImageBox/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$image                    = $options->get_value( 'image' );
		$title                    = $options->get_value( 'title' );
		$description              = $options->get_value( 'description' );
		$title_tag                = $options->get_value( 'title_tag', 'h3' );
		$image_custom_css_classes = $this->get_style_classes_as_string( 'image_styles', [ 'zb-el-imageBox-image' ] );

		if ( ! empty( $image['image'] ) ) : ?>
		<div
			class="zb-el-imageBox-imageWrapper"
		>
			<img
				class="<?php echo esc_attr( $image_custom_css_classes ); ?>"
				src="<?php echo esc_attr( $image['image'] ); ?>"
			/>
		</div>
		<?php endif; ?>
		<span class="zb-el-imageBox-spacer"></span>
		<?php
		if ( ! empty( $title ) || ! empty( $description ) ) {
			echo '<div class="zb-el-imageBox-text">';
			if ( ! empty( $title ) ) {
				$this->render_tag(
					$title_tag,
					'title',
					$title,
					[
						'class' => 'zb-el-imageBox-title',
					]
				);
			}

			if ( ! empty( $description ) ) {
				$this->render_tag(
					'div',
					'description',
					$description,
					[
						'class' => 'zb-el-imageBox-description',
					]
				);
			}

			echo '</div>';
		}
	}
}
