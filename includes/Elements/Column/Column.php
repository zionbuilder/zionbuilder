<?php

namespace ZionBuilder\Elements\Column;

use ZionBuilder\Plugin;
use ZionBuilder\Elements\Element;
use ZionBuilder\Elements\Masks;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Column
 *
 * @package ZionBuilder\Elements
 */
class Column extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_column';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Column', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'layout';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'column', 'container', 'cln', 'column', 'div', 'row' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-column';
	}

	/**
	 * Is wrapper
	 *
	 * Returns true if the element can contain other elements ( f.e. section, column )
	 *
	 * @return boolean The element icon
	 */
	public function is_wrapper() {
		return true;
	}

	public function get_sortable_content_orientation() {
		return 'vertical';
	}

	private function get_column_options() {
		$columns = 12;
		$options = [];

		for ( $i = 1; $i <= 12; $i++ ) {
			$options[] = [
				'id'   => "$i",
				'name' => "$i",
				'size' => 1,
			];
		}

		$options[] = [
			'id'   => '',
			'name' => esc_html__( 'auto', 'zionbuilder' ),
			'size' => 100,
		];
		return $options;
	}

	private function get_column_offset_options() {
		$options = [];

		for ( $i = 0; $i < 12; $i++ ) {
			$options[] = [
				'id'   => "$i",
				'name' => "$i",
			];
		}
		$options[] = [
			'id'   => '',
			'name' => esc_html__( 'auto', 'zionbuilder' ),
			'size' => 100,
		];

		return $options;
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
			'column_size',
			[
				'type'               => 'column_size',
				'description'        => esc_html__( 'Choose the desired column width.', 'zionbuilder' ),
				'title'              => esc_html__( 'Column width', 'zionbuilder' ),
				'default'            => '',
				'options'            => $this->get_column_options(),
				'responsive_options' => true,
				'render_attribute'   => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-column{{RESPONSIVE_DEVICE_CSS}}--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'column_offset',
			[
				'type'               => 'column_size',
				'description'        => esc_html__( 'Choose the desired column offset.', 'zionbuilder' ),
				'title'              => esc_html__( 'Column offset', 'zionbuilder' ),
				'default'            => '',
				'options'            => $this->get_column_offset_options(),
				'responsive_options' => true,
				'render_attribute'   => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-column-offset{{RESPONSIVE_DEVICE_CSS}}--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'link',
			[
				'type'        => 'link',
				'description' => 'Convert this column into a link tag',
				'title'       => __( 'Link', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'tag',
			[
				'type'        => 'select',
				'default'     => 'div',
				'description' => esc_html__( 'Select the HTML tag to use for this element. If you want to add a custom tag, make sure to only use letters and numbers', 'zionbuilder' ),
				'title'       => esc_html__( 'HTML tag', 'zionbuilder' ),
				'addable'     => true,
				'filterable'  => true,
				'options'     => [
					[
						'id'   => 'section',
						'name' => 'Section',
					],
					[
						'id'   => 'div',
						'name' => 'Div',
					],
					[
						'id'   => 'footer',
						'name' => 'Footer',
					],
					[
						'id'   => 'header',
						'name' => 'Header',
					],
					[
						'id'   => 'article',
						'name' => 'Article',
					],
					[
						'id'   => 'main',
						'name' => 'Main',
					],
					[
						'id'   => 'aside',
						'name' => 'Aside',
					],
				],
			]
		);

		$options->add_option(
			'inner_content_layout',
			[
				'type'                    => 'custom_selector',
				'description'             => esc_html__( 'Select the desired content orientation.', 'zionbuilder' ),
				'title'                   => esc_html__( 'Content orientation', 'zionbuilder' ),
				'default'                 => 'column',
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-direction',
				'options'                 => [
					[
						'name' => __( 'vertical', 'zionbuilder' ),
						'id'   => 'column',
					],
					[
						'name' => __( 'horizontal', 'zionbuilder' ),
						'id'   => 'row',
					],
				],
			]
		);

		$options->add_option(
			'inner_content_column_alignment_horizontal',
			[
				'type'                    => 'custom_selector',
				'description'             => esc_html__( 'Inner content horizontal alignment layout', 'zionbuilder' ),
				'title'                   => esc_html__( 'Inner content horizontal alignment', 'zionbuilder' ),
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.align-items',
				'options'                 => [
					[
						'name' => __( 'Left', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'justify-start',
					],
					[
						'name' => __( 'Center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'justify-center',
					],
					[
						'name' => __( 'Right', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'justify-end',
					],
					[
						'name' => __( 'Stretch', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'align-stretch-reversed',
					],
					[
						'name' => __( 'Baseline', 'zionbuilder' ),
						'id'   => 'baseline',
						'icon' => 'align-baseline-reversed',
					],
				],
				'dependency'              => [
					[
						'option' => 'inner_content_layout',
						'value'  => [ 'column' ],
					],
				],
			]
		);

		$options->add_option(
			'inner_content_column_alignment_vertical',
			[
				'type'                    => 'custom_selector',
				'description'             => esc_html__( 'Inner content vertical alignment layout', 'zionbuilder' ),
				'title'                   => esc_html__( 'Inner content vertical alignment', 'zionbuilder' ),
				'columns'                 => 5,
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.justify-content',
				'options'                 => [
					[
						'name' => __( 'Top', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'align-start',
					],
					[
						'name' => __( 'Center', 'zionbuilder' ),
						'id'   => 'Center',
						'icon' => 'align-center',
					],
					[
						'name' => __( 'Bottom', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'align-end',
					],
					[
						'name' => __( 'Space-between', 'zionbuilder' ),
						'id'   => 'space-between',
						'icon' => 'justify-sp-btw-reverse',
					],
					[
						'name' => __( 'Space-around', 'zionbuilder' ),
						'id'   => 'space-around',
						'icon' => 'justify-sp-around-reverse',
					],
				],
				'dependency'              => [
					[
						'option' => 'inner_content_layout',
						'value'  => [ 'column' ],
					],
				],
			]
		);
		$options->add_option(
			'inner_content_row_alignment_horizontal',
			[
				'type'                    => 'custom_selector',
				'description'             => esc_html__( 'Inner content horizontal alignment layout', 'zionbuilder' ),
				'title'                   => esc_html__( 'Inner content horizontal alignment', 'zionbuilder' ),
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.justify-content',
				'options'                 => [
					[
						'name' => __( 'Left', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'justify-start',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'justify-center',
					],
					[
						'name' => __( 'Right', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'justify-end',
					],
					[
						'name' => __( 'space-between', 'zionbuilder' ),
						'id'   => 'space-between',
						'icon' => 'justify-sp-btw',
					],
					[
						'name' => __( 'space-around', 'zionbuilder' ),
						'id'   => 'space-around',
						'icon' => 'justify-sp-around',
					],
				],
				'dependency'              => [
					[
						'option' => 'inner_content_layout',
						'value'  => [ 'row' ],
					],
				],
			]
		);

		$options->add_option(
			'inner_content_row_alignment_vertical',
			[
				'type'                    => 'custom_selector',
				'description'             => esc_html__( 'Inner content vertical alignment layout', 'zionbuilder' ),
				'title'                   => esc_html__( 'Inner content vertical alignment', 'zionbuilder' ),
				'columns'                 => 5,
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.align-items',
				'options'                 => [
					[
						'name' => __( 'Top', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'align-start',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'align-center',
					],
					[
						'name' => __( 'Bottom', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'align-end',
					],
					[
						'name' => __( 'stretch', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'align-stretch',
					],
					[
						'name' => __( 'baseline', 'zionbuilder' ),
						'id'   => 'baseline',
						'icon' => 'align-baseline',
					],
				],
				'dependency'              => [
					[
						'option' => 'inner_content_layout',
						'value'  => [ 'row' ],
					],
				],
			]
		);

		$options->add_option(
			'flex-wrap',
			[
				'type'        => 'custom_selector',
				'grow'        => '5',
				'title'       => esc_html__( 'Wrap content', 'zionbuilder' ),
				'description' => esc_html__( 'If you choose to wrap the content, all inner elements will be placed on a new line if there is no space for them on the same line.', 'zionbuilder' ),
				'options'     => [
					[
						'name' => esc_html__( 'wrap', 'zionbuilder' ),
						'id'   => 'wrap',
					],
					[
						'name' => esc_html__( 'nowrap', 'zionbuilder' ),
						'id'   => 'nowrap',
					],
					[
						'name' => esc_html__( 'wrap-reverse', 'zionbuilder' ),
						'id'   => 'wrap-reverse',
						'icon' => 'reverse',
					],
				],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-wrap',
			]
		);

		$shape_dividers = $options->add_group(
			'shape_dividers',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Shape Dividers', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		$shape_dividers->add_option(
			'shapes',
			[
				'type'  => 'shape_dividers',
				'title' => __( 'Add a mask to your element', 'zionbuilder' ),
			]
		);
	}

	public function get_wrapper_tag( $options ) {
		$link = $options->get_value( 'link', false );

		if ( ! empty( $link['link'] ) ) {
			return 'a';
		}

		return $options->get_value( 'tag', 'div' );
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Column/editor', 'js' ) );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		// Add the grid column class
		$this->render_attributes->add( 'wrapper', 'class', 'zb-column' );
		$link = $options->get_value( 'link', false );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'wrapper', $link );
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
		$masks = $options->get_value( 'shapes' );

		if ( ! empty( $masks ) ) {
			MAsks::render_masks( $masks );
		}

		$this->render_children();
	}
}
