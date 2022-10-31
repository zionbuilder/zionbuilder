<?php

namespace ZionBuilder\Elements\Icon;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Icon
 *
 * @package ZionBuilder\Elements
 */
class Icon extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'icon';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return esc_html__( 'Icon', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'icon', 'icons', 'svg', 'symbol', 'font', 'social', 'social media' ];
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
			'link',
			[
				'type'        => 'link',
				'description' => esc_html__( 'Set the url', 'zionbuilder' ),
				'title'       => esc_html__( 'Link', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'align',
			[
				'type'                    => 'text_align',
				'title'                   => __( 'Align', 'zionbuilder' ),
				'description'             => __( 'Select the desired alignment.', 'zionbuilder' ),
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.text-align',
				'show_responsive_buttons' => true,
			]
		);

		$options->add_option(
			'style',
			[
				'type'             => 'select',
				'description'      => esc_html__( 'Select the desired icon style', 'zionbuilder' ),
				'title'            => esc_html__( 'Style', 'zionbuilder' ),
				'default'          => '',
				'options'          => [
					[
						'id'   => '',
						'name' => esc_html__( 'Default', 'zionbuilder' ),
					],
					[
						'id'   => 'filled',
						'name' => esc_html__( 'Filled', 'zionbuilder' ),
					],
					[
						'id'   => 'bordered',
						'name' => esc_html__( 'Bordered', 'zionbuilder' ),
					],
				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-icon--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'shape',
			[
				'type'             => 'select',
				'description'      => esc_html__( 'Select the desired icon style', 'zionbuilder' ),
				'title'            => esc_html__( 'Shape', 'zionbuilder' ),
				'default'          => 'square',
				'options'          => [
					[
						'id'   => 'square',
						'name' => esc_html__( 'Square', 'zionbuilder' ),
					],
					[
						'id'   => 'circle',
						'name' => esc_html__( 'Circle', 'zionbuilder' ),
					],
					[
						'id'   => 'rounded',
						'name' => esc_html__( 'Rounded', 'zionbuilder' ),
					],
				],
				'dependency'       => [
					[
						'option' => 'style',
						'value'  => [ 'filled', 'bordered' ],
					],
				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-icon--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'border_radius',
			[
				'type'       => 'number_unit',
				'default'    => '0px',
				'title'      => esc_html__( 'Border Radius', 'zionbuilder' ),
				'layout'     => 'inline',
				'min'        => 0,
				'dependency' => [
					[
						'option' => 'shape',
						'value'  => [ 'rounded' ],
					],
				],
				'css_style'  => [
					[
						'selector' => '{{ELEMENT}} .zb-el-icon-icon',
						'value'    => 'border-radius: {{VALUE}}',
					],
				],
			]
		);

		$icon_settings_group = $options->add_option(
			'icon_settings_group',
			[
				'type'  => 'accordion_menu',
				'title' => __( 'Icon Styles', 'zionbuilder' ),
			]
		);

		$icon_pseudo_tab = $icon_settings_group->add_option(
			'icon_pseudo_tab',
			[
				'type' => 'tabs',
			]
		);

		$icon_pseudo_tab_normal = $icon_pseudo_tab->add_option(
			'normal',
			[
				'type'  => 'tab',
				'title' => esc_html__( 'Normal', 'zionbuilder' ),
			]
		);

		$this->add_icon_style_options( $icon_pseudo_tab_normal );

		$icon_pseudo_tab_hover = $icon_pseudo_tab->add_option(
			'hover',
			[
				'type'  => 'tab',
				'title' => esc_html__( 'Hover', 'zionbuilder' ),
			]
		);

		$this->add_icon_style_options( $icon_pseudo_tab_hover, ':hover' );
	}

	private function add_icon_style_options( $options, $selector_suffix = '' ) {
		$options->add_option(
			'icon_color',
			[
				'type'      => 'colorpicker',
				'title'     => __( 'Icon Color', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => sprintf( '{{ELEMENT}} .zb-el-icon-icon%s', $selector_suffix ),
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'shape_color',
			[
				'type'       => 'colorpicker',
				'title'      => __( 'Shape Color', 'zionbuilder' ),
				'layout'     => 'inline',
				'dependency' => [
					[
						'option_path' => 'style',
						'value'       => [ 'filled', 'bordered' ],
					],
				],
				'css_style'  => [
					[
						'selector' => sprintf( '{{ELEMENT}} .zb-el-icon-icon%s', $selector_suffix ),
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'icon_size',
			[
				'type'               => 'slider',
				'title'              => __( 'Icon size', 'zionbuilder' ),
				'default'            => 0,
				'min'                => 0,
				'max'                => 400,
				'content'            => 'px',
				'responsive_options' => true,
				'css_style'          => [
					[
						'selector' => sprintf( '{{ELEMENT}} .zb-el-icon-icon%s', $selector_suffix ),
						'value'    => 'font-size: {{VALUE}}px',
					],
				],
			]
		);

		$options->add_option(
			'icon_shape_size',
			[
				'type'               => 'slider',
				'title'              => __( 'Space around icon', 'zionbuilder' ),
				'default'            => 20,
				'min'                => 0,
				'max'                => 360,
				'content'            => 'px',
				'responsive_options' => true,
				'css_style'          => [
					[
						'selector' => sprintf( '{{ELEMENT}} .zb-el-icon-icon%s', $selector_suffix ),
						'value'    => 'padding: {{VALUE}}px',
					],
				],
			]
		);

		$options->add_option(
			'icon_rotate',
			[
				'type'      => 'slider',
				'title'     => __( 'Icon rotate', 'zionbuilder' ),
				'default'   => 0,
				'min'       => 0,
				'max'       => 360,
				'content'   => 'deg',
				'css_style' => [
					[
						'selector' => sprintf( '{{ELEMENT}} .zb-el-icon-icon%s', $selector_suffix ),
						'value'    => 'transform: rotate({{VALUE}}deg)',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Icon/editor', 'js' ) );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Icon/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$link     = $options->get_value( 'link', false );
		$html_tag = 'span';
		$icon     = $options->get_value( 'icon' );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'shape', $link );
			$html_tag = 'a';
		}

		// Set the icon attributes
		$this->attach_icon_attributes( 'shape', $icon );
		$this->render_attributes->add( 'shape', 'class', 'zb-el-icon-icon' );

		$this->render_tag( $html_tag, 'shape' );
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
			'icon_styles',
			[
				'title'      => esc_html__( 'Icon styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-icon-icon',
				'render_tag' => 'shape',
			]
		);
	}
}
