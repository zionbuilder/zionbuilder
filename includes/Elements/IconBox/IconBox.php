<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class IconBox extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'icon_box';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return esc_html__( 'Icon Box', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'icon', 'iconbox', 'box' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-icon';
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
			'icon',
			[
				'type'        => 'icon_library',
				'id'          => 'icon',
				'title'       => esc_html__( 'Icon', 'zionbuilder' ),
				'description' => esc_html__( 'Choose an icon', 'zionbuilder' ),
				'default'     => [
					'family'  => 'Font Awesome 5 Brands Regular',
					'name'    => 'wordpress-simple',
					'unicode' => 'uf411',
				],
			]
		);

		$options->add_option(
			'position',
			[
				'type'             => 'select',
				'title'            => esc_html__( 'Icon position', 'zionbuilder' ),
				'description'      => esc_html__( 'Set the desired timing function for the transition.', 'zionbuilder' ),
				'options'          => [
					[
						'name' => esc_html__( 'Icon on top', 'zionbuilder' ),
						'id'   => 'top',
					],
					[
						'name' => esc_html__( 'Icon on left', 'zionbuilder' ),
						'id'   => 'left',
					],
					[
						'name' => esc_html__( 'Icon on right', 'zionbuilder' ),
						'id'   => 'right',
					],
				],
				'default'          => 'left',
				'render_attribute' => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-el-iconBox--icon-{{VALUE}}',
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
				'description' => esc_html__( 'Set the desired title.' ),
				'title'       => esc_html__( 'Title', 'zionbuilder' ),
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
						'name' => __( 'h4', 'zionbuilder' ),
						'id'   => 'h4',
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
			'icon_styles',
			[
				'title'      => esc_html__( 'Icon styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-iconBox-icon',
				'render_tag' => 'icon',
			]
		);
		$this->register_style_options_element(
			'title_styles',
			[
				'title'      => esc_html__( 'Title styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-iconBox-title',
				'render_tag' => 'title',
			]
		);
		$this->register_style_options_element(
			'description_styles',
			[
				'title'      => esc_html__( 'Description styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-iconBox-description',
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
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/IconBox/editor.js' ) );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/IconBox/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$icon                     = $options->get_value( 'icon' );
		$title_tag                = $options->get_value( 'title_tag', 'h3' );
		$description              = $options->get_value( 'description' );
		$title                    = $options->get_value( 'title' );
		$image_custom_css_classes = $this->get_style_classes_as_string( 'image_styles', [ 'zb-el-imageBox-image' ] );

		if ( $icon ) {
			?>
			<div class="zb-el-iconBox-iconWrapper">
				<?php
					$this->attach_icon_attributes( 'icon', $icon );
				$this->render_tag( 'span', 'icon', false, [ 'class' => 'zb-el-iconBox-icon' ] );
				?>
			</div>
			<?php
		}
		?>
		<span class="zb-el-iconBox-spacer"></span>
		<div class="zb-el-iconBox-text">
			<?php
			if ( ! empty( $title ) ) {
				$this->render_tag( $title_tag, 'title', wp_kses_post( $title ), [ 'class' => 'zb-el-iconBox-title' ] );
			}
			?>
			<?php
			if ( ! empty( $description ) ) {
				$this->render_tag( 'div', 'description', wp_kses_post( $description ), [ 'class' => 'zb-el-iconBox-description' ] );
			}
			?>
		</div>
		<?php
	}

}
