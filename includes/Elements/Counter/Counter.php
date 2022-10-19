<?php

namespace ZionBuilder\Elements\Counter;

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
class Counter extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'counter';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Counter', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'counter', 'animated', 'progress', 'number', 'stats' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-counter-free';
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
			'start',
			[
				'type'             => 'number',
				'title'            => esc_html__( 'Number: from', 'zionbuilder' ),
				'description'      => esc_html__( 'Set the desired starting number', 'zionbuilder' ),
				'default'          => 0,
				'min'              => 0,
				'max'              => 10000000,
				'render_attribute' => [
					[
						'attribute' => 'data-start',
					],
				],
			]
		);

		$options->add_option(
			'end',
			[
				'type'             => 'number',
				'title'            => esc_html__( 'Number: to', 'zionbuilder' ),
				'description'      => esc_html__( 'Set the desired starting number', 'zionbuilder' ),
				'default'          => 100,
				'min'              => 0,
				'max'              => 99999999,
				'render_attribute' => [
					[
						'attribute' => 'data-end',
					],
				],
			]
		);

		$options->add_option(
			'duration',
			[
				'type'             => 'number',
				'title'            => esc_html__( 'Speed', 'zionbuilder' ),
				'description'      => esc_html__( 'Set the desired speed for the animation', 'zionbuilder' ),
				'default'          => 2000,
				'min'              => 0,
				'max'              => 40000,
				'render_attribute' => [
					[
						'attribute' => 'data-duration',
					],
				],
			]
		);

		$options->add_option(
			'before',
			[
				'type'  => 'text',
				'title' => esc_html__( 'Text before', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'after',
			[
				'type'  => 'text',
				'title' => esc_html__( 'Text after', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'align',
			[
				'type'               => 'custom_selector',
				'title'              => esc_html__( 'Align', 'zionbuilder' ),
				'description'        => esc_html__( 'Select the desired alignment.', 'zionbuilder' ),
				'columns'            => 3,
				'options'            => [
					[
						'id'   => __( 'left', 'zionbuilder' ),
						'icon' => 'align--left',
					],
					[
						'id'   => __( 'center', 'zionbuilder' ),
						'icon' => 'align--center',
					],
					[
						'id'   => __( 'right', 'zionbuilder' ),
						'icon' => 'align--right',
					],
				],
				'responsive_options' => true,
				'render_attribute'   => [
					[
						'tag_id'    => 'wrapper',
						'attribute' => 'class',
						'value'     => 'zb-el-counter{{RESPONSIVE_DEVICE_CSS}}--align--{{VALUE}}',
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
			'before_text_styles',
			[
				'title'    => esc_html__( 'Before styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-counter__before',
			]
		);
		$this->register_style_options_element(
			'after_text_styles',
			[
				'title'    => esc_html__( 'After styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-counter__after',
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
		wp_enqueue_script( 'zb-element-counter', Plugin::instance()->scripts->get_script_url( 'elements/Counter/frontend', 'js' ), [], Plugin::instance()->get_version(), true );
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Counter/editor', 'js' ) );

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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Counter/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$before = $options->get_value( 'before' );
		$after  = $options->get_value( 'after' );
		$start  = $options->get_value( 'start' );

		if ( ! empty( $before ) ) {
			printf( '<div class="%s">%s</div>', esc_attr( $this->get_style_classes_as_string( 'before_text_styles', [ 'zb-el-counter__before' ] ) ), wp_kses_post( $before ) );
		} ?>
		<div class="zb-el-counter__number"><?php echo wp_kses_post( $start ); ?></div>
		<?php

		if ( ! empty( $after ) ) {
			printf( '<div class="%s">%s</div>', esc_attr( $this->get_style_classes_as_string( 'after_text_styles', [ 'zb-el-counter__after' ] ) ), wp_kses_post( $after ) );
		}
	}
}
