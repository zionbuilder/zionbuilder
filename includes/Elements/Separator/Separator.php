<?php

namespace ZionBuilder\Elements\Separator;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Separator
 *
 * @package ZionBuilder\Elements
 */
class Separator extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_separator';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Separator', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'separator', 'divider', 'line', 'spacer', 'horizontal', 'rule', 'hr', 'spacing', 'distance', 'margin', 'padding', 'decorative' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-divider';
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
			'style',
			[
				'type'      => 'select',
				'title'     => __( 'Separator Type', 'zionbuilder' ),
				'default'   => 'solid',
				'options'   => [
					[
						'id'   => 'solid',
						'name' => 'solid',
					],
					[
						'id'   => 'double',
						'name' => 'double',
					],
					[
						'id'   => 'dashed',
						'name' => 'dashed',
					],
					[
						'id'   => 'dotted',
						'name' => 'dotted',
					],
				],
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-zionSeparator-item',
						'value'    => 'border-top-style: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'alignement',
			[
				'type'    => 'custom_selector',
				'title'   => __( 'Alignment', 'zionbuilder' ),
				'columns' => 3,
				'default' => 'center',
				'options' => [
					[
						'name' => __( 'flex-start', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'align--left',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'align--center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'align--right',
					],
				],
				'sync'    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.%%PSEUDO_SELECTOR%%.justify-content',
			]
		);

		$options->add_option(
			'stroke_color',
			[
				'type'      => 'colorpicker',
				'title'     => __( 'Line color', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-zionSeparator-item',
						'value'    => 'border-top-color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'width',
			[
				'type'        => 'dynamic_slider',
				'default'     => '100%',
				'title'       => __( 'Width', 'zionbuilder' ),
				'description' => __( 'Set separator width.', 'zionbuilder' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 9999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'css_style'   => [
					[
						'selector' => '{{ELEMENT}} .zb-el-zionSeparator-item--size',
						'value'    => 'width: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'stroke_width',
			[
				'type'        => 'slider',
				'default'     => 5,
				'min'         => 0,
				'max'         => 25,
				'title'       => __( 'Line thickness', 'zionbuilder' ),
				'description' => __( 'Set stroke width.', 'zionbuilder' ),
				'css_style'   => [
					[
						'selector' => '{{ELEMENT}} .zb-el-zionSeparator-item',
						'value'    => 'border-top-width: {{VALUE}}px',
					],
				],
			]
		);

		$options->add_group(
			'padding',
			[
				'type'                    => 'dimensions',
				'title'                   => __( 'Padding', 'zionbuilder' ),
				'description'             => __( 'Choose the desired padding for this element.', 'zionbuilder' ),
				'min'                     => 0,
				'max'                     => 99999,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default',
				'show_responsive_buttons' => true,
				'dimensions'              => [
					[
						'name' => 'top',
						'icon' => 'padding-top',
						'id'   => 'padding-top',
					],
					[
						'name' => 'right',
						'icon' => 'padding-right',
						'id'   => 'padding-right',
					],
					[
						'name' => 'bottom',
						'icon' => 'padding-bottom',
						'id'   => 'padding-bottom',
					],
					[
						'name' => 'left',
						'icon' => 'padding-left',
						'id'   => 'padding-left',
					],
				],
			]
		);

		$options->add_group(
			'margin',
			[
				'type'                    => 'dimensions',
				'title'                   => __( 'Margin', 'zionbuilder' ),
				'description'             => __( 'Choose the desired margin for this element.', 'zionbuilder' ),
				'min'                     => -99999,
				'max'                     => 99999,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default',
				'show_responsive_buttons' => true,
				'dimensions'              => [
					[
						'name' => 'top',
						'icon' => 'margin-top',
						'id'   => 'margin-top',
					],
					[
						'name' => 'right',
						'icon' => 'margin-right',
						'id'   => 'margin-right',
					],
					[
						'name' => 'bottom',
						'icon' => 'margin-bottom',
						'id'   => 'margin-bottom',
					],
					[
						'name' => 'left',
						'icon' => 'margin-left',
						'id'   => 'margin-left',
					],
				],
			]
		);

		$options->add_option(
			'use_icon',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Use icon', 'zionbuilder' ),
			]
		);

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
				'dependency'  => [
					[
						'option' => 'use_icon',
						'value'  => [ true ],
					],
				],
			]
		);

		$options->add_option(
			'icon_color',
			[
				'type'       => 'colorpicker',
				'title'      => __( 'Icon Color', 'zionbuilder' ),
				'layout'     => 'inline',
				'dependency' => [
					[
						'option' => 'use_icon',
						'value'  => [ true ],
					],
				],
				'css_style'  => [
					[
						'selector' => '{{ELEMENT}} .zb-el-zionSeparator-icon',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'icon_size',
			[
				'type'       => 'slider',
				'default'    => 24,
				'title'      => __( 'Icon size', 'zionbuilder' ),
				'dependency' => [
					[
						'option' => 'use_icon',
						'value'  => [ true ],
					],
				],
				'css_style'  => [
					[
						'selector' => '{{ELEMENT}} .zb-el-zionSeparator-icon',
						'value'    => 'font-size: {{VALUE}}px',
					],
				],
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Separator/editor', 'js' ) );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Separator/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$use_icon = $options->get_value( 'use_icon' );
		$icon     = $options->get_value( 'icon' );

		if ( ! $use_icon ) {
			$this->render_tag( 'div', 'separator_item', '', [ 'class' => [ 'zb-el-zionSeparator-item zb-el-zionSeparator-item--size' ] ] );
		} else {
			?>
				<div class="zb-el-zionSeparator-item-icon zb-el-zionSeparator-item--size">
					<span class="zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-one"></span>
					<?php
						$this->attach_icon_attributes( 'icon', $icon );
						$this->render_tag( 'span', 'icon', '', [ 'class' => 'zb-el-zionSeparator-icon' ] );
					?>
					<span class="zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-two" ></span>
				</div>
			<?php
		}
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
			'separator_item',
			[
				'title'      => esc_html__( 'Icon styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-zionSeparator-item',
				'render_tag' => 'separator_item',
			]
		);

	}
}
