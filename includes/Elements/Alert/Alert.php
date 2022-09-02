<?php

namespace ZionBuilder\Elements\Alert;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class Alert extends Element {

	/**
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'alert';
	}

	/**
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Alert', 'zionbuilder' );
	}

	/**
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'alert', 'info', 'success', 'message', 'notification', 'warning', 'advertisment', 'news' ];
	}

	/**
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-alert';
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
			'type',
			[
				'type'             => 'select',
				'title'            => esc_html__( 'Type', 'zionbuilder' ),
				'default'          => 'info',
				'options'          => [
					[
						'name' => esc_html__( 'Info', 'zionbuilder' ),
						'id'   => 'info',
					],
					[
						'name' => esc_html__( 'Success', 'zionbuilder' ),
						'id'   => 'success',
					],
					[
						'name' => esc_html__( 'Danger', 'zionbuilder' ),
						'id'   => 'danger',
					],
					[
						'name' => esc_html__( 'Warning', 'zionbuilder' ),
						'id'   => 'warning',
					],
				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-alert--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'title',
			[
				'type'    => 'text',
				'title'   => esc_html__( 'Title', 'zionbuilder' ),
				'default' => esc_html__( 'This is an Alert', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'title_color',
			[
				'type'      => 'colorpicker',
				'width'     => 50,
				'title'     => __( 'Title color', 'zionbuilder' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-alert__title',
						'value'    => 'color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'title_border_color',
			[
				'type'      => 'colorpicker',
				'width'     => 50,
				'title'     => __( 'Title border color', 'zionbuilder' ),
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-alert__title',
						'value'    => 'border-color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'description',
			[
				'type'    => 'editor',
				'title'   => esc_html__( 'Description', 'zionbuilder' ),
				'default' => esc_html__( 'This is just a dummy text, edit the element options to set your own text', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'show_dismiss',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Show dismiss button?', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'dismiss_icon_color',
			[
				'type'       => 'colorpicker',
				'title'      => __( 'Dismiss icon color', 'zionbuilder' ),
				'css_style'  => [
					[
						'selector' => '{{ELEMENT}} .zb-el-alert__closeIcon',
						'value'    => 'color: {{VALUE}}',
					],
				],
				'dependency' => [
					[
						'option' => 'show_dismiss',
						'value'  => [ true ],
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Alert/editor', 'js' ) );
		wp_enqueue_script( 'zb-element-alert', Plugin::instance()->scripts->get_script_url( 'elements/Alert/frontend', 'js' ), [], Plugin::instance()->get_version(), true );

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
		$this->enqueue_editor_style( Utils::get_file_url( 'dist/elements/Alert/editor.css' ) );
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Alert/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$title        = $options->get_value( 'title' );
		$description  = $options->get_value( 'description' );
		$show_dismiss = $options->get_value( 'show_dismiss' );

		if ( $show_dismiss ) {
			echo '<span class="zb-el-alert__closeIcon"></span>';
		}

		if ( ! empty( $title ) ) {
			printf( '<span class="zb-el-alert__title">%s</span>', $title ); // phpcs:ignore WordPress.Security.EscapeOutput
		}

		if ( ! empty( $description ) ) {
			printf( '<div class="zb-el-alert__description">%s</div>', $description ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}
