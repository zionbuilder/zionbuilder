<?php

namespace ZionBuilder\Elements\ProgressBars;

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
class ProgressBars extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'progress_bars';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Progress Bars', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'counter', 'progress', 'bar', 'skills', 'percentage', 'graph', 'stats', 'statistics', 'prgs' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-bar-counter';
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$bars = $options->add_option(
			'bars',
			[
				'type'               => 'repeater',
				'add_button_text'    => __( 'Add new progress bar', 'zionbuilder' ),
				'item_title'         => 'title',
				'default_item_title' => 'item %s',
				'default'            => [
					[
						'title'           => esc_html__( 'Bar 1', 'zionbuilder' ),
						'fill_percentage' => 50,
					],
					[
						'title'           => esc_html__( 'Bar 2', 'zionbuilder' ),
						'fill_percentage' => 80,
					],
				],
				'add_template'       => [
					'title'           => esc_html__( 'Progress Bar', 'zionbuilder' ),
					'fill_percentage' => 50,
				],
			]
		);

		$bars->add_option(
			'title',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Title', 'zionbuilder' ),
				'description' => esc_html__( 'Set the desired bar title', 'zionbuilder' ),
			]
		);

		$bars->add_option(
			'fill_percentage',
			[
				'type'    => 'slider',
				'title'   => __( 'Fill percentage', 'zionbuilder' ),
				'default' => 50,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'content' => '%',
			]
		);

		$bars->add_option(
			'bar_color',
			[
				'type'      => 'colorpicker',
				'title'     => esc_html__( 'Bar progress color', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-progressBars__bar--{{INDEX}} .zb-el-progressBars__barProgress',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$bars->add_option(
			'bar_background_color',
			[
				'type'      => 'colorpicker',
				'title'     => esc_html__( 'Bar background color', 'zionbuilder' ),
				'layout'    => 'inline',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-progressBars__bar--{{INDEX}} .zb-el-progressBars__barTrack',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$bars->add_option(
			'bar_style',
			[
				'type'             => 'select',
				'title'            => esc_html__( 'Select the desired bar style', 'zionbuilder' ),
				'default'          => 'square',
				'options'          => [
					[
						'name' => esc_html__( 'Square', 'zionbuilder' ),
						'id'   => 'square',
					],
					[
						'name' => esc_html__( 'Rounded', 'zionbuilder' ),
						'id'   => 'rounded',
					],
					[
						'name' => esc_html__( 'Circle', 'zionbuilder' ),
						'id'   => 'circle',
					],
				],
				'render_attribute' => [
					[
						'tag_id'    => 'single-bar',
						'attribute' => 'class',
						'value'     => 'zb-el-progressBars__barTrack--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'bars_color',
			[
				'type'      => 'colorpicker',
				'title'     => esc_html__( 'Bar progress color', 'zionbuilder' ),
				'layout'    => 'inline',
				'default'   => '#06bee1',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-progressBars__barProgress',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'bars_background_color',
			[
				'type'      => 'colorpicker',
				'title'     => esc_html__( 'Bar background color', 'zionbuilder' ),
				'layout'    => 'inline',
				'default'   => '#c4c4c4',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-progressBars__barTrack',
						'value'    => 'background-color: {{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'bars_height',
			[
				'type'      => 'slider',
				'title'     => __( 'Bars height', 'zionbuilder' ),
				'default'   => 13,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'content'   => 'px',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-progressBars__barProgress',
						'value'    => 'height: {{VALUE}}px',
					],
				],
			]
		);

		$options->add_option(
			'bars_distance',
			[
				'type'      => 'slider',
				'title'     => __( 'Items distance', 'zionbuilder' ),
				'default'   => 7,
				'min'       => 0,
				'max'       => 100,
				'step'      => 1,
				'content'   => 'px',
				'css_style' => [
					[
						'selector' => '{{ELEMENT}} .zb-el-progressBars__singleBar',
						'value'    => 'margin-bottom: {{VALUE}}px',
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
			'title_styles',
			[
				'title'    => esc_html__( 'Title styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-progressBars__barTitle',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/ProgressBars/editor', 'js' ) );
		wp_enqueue_script( 'zb-element-progress-bars', Utils::get_file_url( 'dist/elements/ProgressBars/frontend.js' ), [], Plugin::instance()->get_version(), true );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/ProgressBars/frontend.css' ) );
	}
	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		$this->set_wrapper_tag( 'ul' );
	}
	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {

		$this->render_tag_group(
			'li',
			'bars',
			'single-bar',
			[
				'render_callback' => [ $this, 'render_single_bar' ],
				'attributes'      => [
					'class' => 'zb-el-progressBars__bar--{{INDEX}}',
				],
			]
		);
	}

	public function render_single_bar( $bar_settings ) {
		$defaults = [
			'title'           => '',
			'fill_percentage' => 50,
			'bar_style'       => 'square',
		];

		$settings        = wp_parse_args( $bar_settings, $defaults );
		$title           = $settings['title'];
		$fill_percentage = $settings['fill_percentage'];

		ob_start(); ?>

		<?php if ( ! empty( $title ) ) : ?>
		<h5  <?php echo $this->render_attributes->get_attributes_as_string( 'title_styles', [ 'class' => 'zb-el-progressBars__barTitle' ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
>
			<?php echo wp_kses_post( $title ); ?>
		</h5>
		<?php endif; ?>
		<span class="zb-el-progressBars__barTrack">
			<span
				class="zb-el-progressBars__barProgress"
				data-width="<?php echo esc_attr( $fill_percentage ); ?>"
			>
			</span>
		</span>
		<?php

		return ob_get_clean();
	}
}
