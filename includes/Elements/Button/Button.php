<?php

namespace ZionBuilder\Elements\Button;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Button
 *
 * @package ZionBuilder\Elements
 */
class Button extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_button';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Button', 'zionbuilder' );
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'button', 'link', 'action', 'call to action' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-button';
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
			'button_text',
			[
				'type'        => 'text',
				'description' => __( 'Set the button text', 'zionbuilder' ),
				'title'       => __( 'Button text', 'zionbuilder' ),
				'default'     => __( 'Press me', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'icon',
			[
				'type'        => 'icon_library',
				'title'       => __( 'Icon', 'zionbuilder' ),
				'description' => __( 'Choose an icon', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'link',
			[
				'type'        => 'link',
				'description' => __( 'Set the url', 'zionbuilder' ),
				'title'       => __( 'Link', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'button_type',
			[
				'type'             => 'select',
				'title'            => __( 'Button type', 'zionbuilder' ),
				'default'          => 'primary',
				'options'          => [
					[
						'name' => __( 'Primary', 'zionbuilder' ),
						'id'   => 'primary',
					],
					[
						'name' => __( 'Black', 'zionbuilder' ),
						'id'   => 'black',
					],
					[
						'name' => __( 'Dark', 'zionbuilder' ),
						'id'   => 'dark',
					],
					[
						'name' => __( 'Gray', 'zionbuilder' ),
						'id'   => 'gray',
					],
					[
						'name' => __( 'White', 'zionbuilder' ),
						'id'   => 'white',
					],
					[
						'name' => __( 'Success', 'zionbuilder' ),
						'id'   => 'succes',
					],
					[
						'name' => __( 'Danger', 'zionbuilder' ),
						'id'   => 'danger',
					],
					[
						'name' => __( 'Warning', 'zionbuilder' ),
						'id'   => 'warning',
					],
					[
						'name' => __( 'Info', 'zionbuilder' ),
						'id'   => 'info',
					],
					[
						'name' => __( 'Link', 'zionbuilder' ),
						'id'   => 'link',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'button_styles',
						'attribute' => 'class',
						'value'     => 'zb-el-button--icon-{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'button_shape',
			[
				'type'             => 'custom_selector',
				'title'            => __( 'Button shape', 'zionbuilder' ),
				'description'      => __( 'Choose button shape.' ),
				'default'          => 'left',
				'columns'          => 3,
				'options'          => [
					[
						'name' => __( 'Normal', 'zionbuilder' ),
						'id'   => 'normal',
					],
					[
						'name' => __( 'Semi rounded', 'zionbuilder' ),
						'id'   => 'semi-rounded',
					],
					[
						'name' => __( 'Rounded', 'zionbuilder' ),
						'id'   => 'rounded',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'button_styles',
						'attribute' => 'class',
						'value'     => 'zb-el-button--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'button_size',
			[
				'type'             => 'custom_selector',
				'title'            => __( 'Button size', 'zionbuilder' ),
				'description'      => __( 'Choose button size.' ),
				'default'          => 'normal',
				'columns'          => 2,
				'options'          => [
					[
						'name' => __( 'Small', 'zionbuilder' ),
						'id'   => 'small',
					],
					[
						'name' => __( 'Normal', 'zionbuilder' ),
						'id'   => 'normal',
					],
					[
						'name' => __( 'Medium', 'zionbuilder' ),
						'id'   => 'medium',
					],
					[
						'name' => __( 'Large', 'zionbuilder' ),
						'id'   => 'large',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'button_styles',
						'attribute' => 'class',
						'value'     => 'zb-el-button--size-{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'full_width',
			[
				'type'             => 'checkbox_switch',
				'default'          => false,
				'layout'           => 'inline',
				'title'            => esc_html__( 'Full width', 'zionbuilder' ),
				'render_attribute' => [
					[
						'tag_id'    => 'button_styles',
						'attribute' => 'class',
						'value'     => 'zb-el-button--full-width',
					],
				],
			]
		);

		$options->add_option(
			'button_position',
			[
				'type'               => 'custom_selector',
				'title'              => __( 'Button position', 'zionbuilder' ),
				'description'        => __( 'Choose button position.' ),
				'columns'            => 3,
				'options'            => [
					[
						'id'   => 'left',
						'icon' => 'align--left',
					],
					[
						'id'   => 'center',
						'icon' => 'align--center',
					],
					[
						'id'   => __( 'right', 'zionbuilder' ),
						'icon' => 'align--right',
					],
				],
				'responsive_options' => true,
				'dependency'         => [
					[
						'option' => 'full_width',
						'value'  => [ false ],
					],
				],
				'render_attribute'   => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-el-zionButton{{RESPONSIVE_DEVICE_CSS}}--align--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'icon_position',
			[
				'type'             => 'custom_selector',
				'title'            => __( 'Icon position', 'zionbuilder' ),
				'description'      => __( 'Choose icon position.' ),
				'default'          => 'left',
				'columns'          => 2,
				'options'          => [
					[
						'id'   => 'left',
						'name' => 'left',
					],
					[
						'name' => __( 'right', 'zionbuilder' ),
						'id'   => 'right',
					],
					[
						'name' => __( 'top', 'zionbuilder' ),
						'id'   => 'top',
					],
					[
						'name' => __( 'bottom', 'zionbuilder' ),
						'id'   => 'bottom',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'button_styles',
						'attribute' => 'class',
						'value'     => 'zb-el-button--icon-{{VALUE}}',
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
			'button_styles',
			[
				'title'      => esc_html__( 'Button styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-button',
				'render_tag' => 'button_styles',
			]
		);
		$this->register_style_options_element(
			'icon_styles',
			[
				'title'      => esc_html__( 'Icon styles', 'zionbuilder' ),
				'selector'   => '{{ELEMENT}} .zb-el-button__icon',
				'render_tag' => 'icon',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Button/editor', 'js' ) );
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
		$this->enqueue_element_style( Plugin::instance()->scripts->get_script_url( 'elements/Button/frontend', 'css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$html_tag         = 'span';
		$icon_html        = '';
		$button_text_html = '';
		$button_text      = $options->get_value( 'button_text', false );
		$icon             = $options->get_value( 'icon', false );
		$link             = $options->get_value( 'link', false );

		$combined_button_attr = $this->render_attributes->get_combined_attributes( 'button_styles', [ 'class' => 'zb-el-button' ] );
		$combined_icon_attr   = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zb-el-button__icon' ] );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'button', $link );
			$html_tag = 'a';

		}

		if ( ! empty( $icon ) ) {
			$this->attach_icon_attributes( 'icon', $icon );
			$icon_html = $this->get_render_tag(
				'span',
				'icon',
				'',
				$combined_icon_attr
			);
		}

		if ( ! empty( $button_text ) ) {
			$button_text_html = $this->get_render_tag(
				'span',
				'button_text',
				$button_text,
				[
					'class' => 'zb-el-button__text',
				]
			);
		}

		$this->render_tag(
			$html_tag,
			'button',
			[ $icon_html, $button_text_html ],
			$combined_button_attr
		);
	}
}
