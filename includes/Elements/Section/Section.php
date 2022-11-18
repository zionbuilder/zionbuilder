<?php

namespace ZionBuilder\Elements\Section;

use ZionBuilder\Elements\Element;
use ZionBuilder\Elements\Masks;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Section
 *
 * @package ZionBuilder\Elements
 */
class Section extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_section';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Section', 'zionbuilder' );
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
		return [ 'section', 'container', 'div', 'row', 'layout', 'sct' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-section';
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

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'inner_content_width',
			[
				'type'             => 'select',
				'description'      => esc_html__( 'Select the desired inner content type', 'zionbuilder' ),
				'title'            => esc_html__( 'Inner content width', 'zionbuilder' ),
				'default'          => '',
				'options'          => [
					[
						'id'   => '',
						'name' => esc_html__( 'Boxed', 'zionbuilder' ),
					],
					[
						'id'   => 'full',
						'name' => esc_html__( 'Full size', 'zionbuilder' ),
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'inner_content_styles',
						'attribute' => 'class',
						'value'     => 'zb-flex-width--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'inner_content_width_value',
			[
				'type'               => 'dynamic_slider',
				'description'        => esc_html__( 'Set the desired Inner Content width', 'zionbuilder' ),
				'default_step'       => 1,
				'default_shift_step' => 5,
				'title'              => esc_html__( 'Inner content width', 'zionbuilder' ),
				'responsive_options' => true,
				'default'            => [
					'default' => '1120px',
				],
				'options'            => [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 5,
						'unit'       => '%',
					],
					[
						'min'        => 0,
						'max'        => 2000,
						'step'       => 1,
						'shift_step' => 25,
						'unit'       => 'px',
					],
					[
						'unit' => 'auto',
					],
				],
				'dependency'         => [
					[
						'option' => 'inner_content_width',
						'value'  => [ '' ],
					],
				],
				'css_style'          => [
					[
						'selector' => '{{ELEMENT}} .zb-section__innerWrapper',
						'value'    => 'max-width: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'columns_gap',
			[
				'type'               => 'select',
				'description'        => esc_html__( 'Set the desired columns gap. This will affect all inner columns.', 'zionbuilder' ),
				'title'              => esc_html__( 'Columns gap', 'zionbuilder' ),
				'default'            => 'default',
				'responsive_options' => true,
				'options'            => [
					[
						'id'   => 'default',
						'name' => esc_html__( 'Default (15px)', 'zionbuilder' ),
					],
					[
						'id'   => 'xsmall',
						'name' => esc_html__( 'Extra small (5px)', 'zionbuilder' ),
					],
					[
						'id'   => 'small',
						'name' => esc_html__( 'Small (10px)', 'zionbuilder' ),
					],
					[
						'id'   => 'large',
						'name' => esc_html__( 'Large (25px)', 'zionbuilder' ),
					],
					[
						'id'   => 'xlarge',
						'name' => esc_html__( 'Extra Large (40px)', 'zionbuilder' ),
					],
					[
						'id'   => 'no',
						'name' => esc_html__( 'No gap', 'zionbuilder' ),
					],
				],
				'render_attribute'   => [
					[
						'tag_id'    => 'inner_content_styles',
						'attribute' => 'class',
						'value'     => 'zb-sct-clm-gap{{RESPONSIVE_DEVICE_CSS}}--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'tag',
			[
				'type'        => 'select',
				'description' => esc_html__( 'Select the HTML tag to use for this element. If you want to add a custom tag, make sure to only use letters and numbers', 'zionbuilder' ),
				'title'       => esc_html__( 'HTML tag', 'zionbuilder' ),
				'default'     => 'section',
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
				'default'                 => 'row',
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.inner_content_styles.styles.%%RESPONSIVE_DEVICE%%.default.flex-direction',
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
				'sync'                    => '_styles.inner_content_styles.styles.%%RESPONSIVE_DEVICE%%.default.align-items',
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
						'name' => __( 'stretch', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'align-stretch-reversed',
					],
					[
						'name' => __( 'baseline', 'zionbuilder' ),
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
				'sync'                    => '_styles.inner_content_styles.styles.%%RESPONSIVE_DEVICE%%.default.justify-content',
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
						'name' => __( 'space-between', 'zionbuilder' ),
						'id'   => 'space-between',
						'icon' => 'justify-sp-btw-reverse',
					],
					[
						'name' => __( 'space-around', 'zionbuilder' ),
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
				'sync'                    => '_styles.inner_content_styles.styles.%%RESPONSIVE_DEVICE%%.default.justify-content',
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
				'sync'                    => '_styles.inner_content_styles.styles.%%RESPONSIVE_DEVICE%%.default.align-items',
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
			'inner_content_styles',
			[
				'title'      => esc_html__( 'Inner Content', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-section__innerWrapper',
				'render_tag' => 'inner_content_styles',
			]
		);
	}

	public function get_wrapper_tag( $options ) {
		return $options->get_value( 'tag', 'section' );
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Section/editor', 'js' ) );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		// Add the grid section class
		$this->render_attributes->add( 'wrapper', 'class', 'zb-section' );
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

		$this->render_attributes->add( 'inner_content_styles', 'class', 'zb-section__innerWrapper' );
		$this->render_tag( 'div', 'inner_content_styles', $this->get_children_for_render() );
	}
}
