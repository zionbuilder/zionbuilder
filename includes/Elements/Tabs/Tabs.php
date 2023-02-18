<?php

namespace ZionBuilder\Elements\Tabs;

use ZionBuilder\Plugin;
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
class Tabs extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'tabs';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Tabs', 'zionbuilder' );
	}

	/**
	 * Is wrapper
	 *
	 * Returns true if the element can contain other elements ( f.e. section, column )
	 *
	 * @return boolean The element icon
	 */
	public function is_wrapper() {
		return Utils::is_pro_installed();
	}


	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'tab', 'folder', 'navigation', 'tabbar', 'steps' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-tabs';
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
			'tabs',
			[
				'type'         => 'child_adder',
				'title'        => __( 'Tabs', 'zionbuilder' ),
				'child_type'   => 'tabs_item',
				'item_name'    => 'title',
				'min'          => 1,
				'add_template' => [
					'element_type' => 'tabs_item',
					'options'      => [
						'title' => __( 'Tab', 'zionbuilder' ),
					],
				],
				'default'      => [
					[
						'element_type' => 'tabs_item',
						'options'      => [
							'title' => sprintf( '%s 1', __( 'Tab', 'zionbuilder' ) ),
						],
					],
					[
						'element_type' => 'tabs_item',
						'options'      => [
							'title' => sprintf( '%s 2', __( 'Tab', 'zionbuilder' ) ),
						],
					],
				],
			]
		);

		$options->add_option(
			'layout',
			[
				'type'             => 'select',
				'default'          => '',
				'title'            => __( 'Layout', 'zionbuilder' ),
				'options'          => [
					[
						'name' => __( 'Horizontal', 'zionbuilder' ),
						'id'   => '',
					],
					[
						'name' => __( 'Vertical', 'zionbuilder' ),
						'id'   => 'vertical',
					],
					[
						'name' => __( 'Stacked', 'zionbuilder' ),
						'id'   => 'stacked',
					],
				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-tabs--{{VALUE}}',
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
			'inner_content_styles_title',
			[
				'title'    => esc_html__( 'Tab title styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-tabs-nav-title',
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_active_tab',
			[
				'title'                   => esc_html__( 'Active tab title styles', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zb-el-tabs-nav-title.zb-el-tabs-nav--active',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_content',
			[
				'title'    => esc_html__( 'Content styles', 'zionbuilder' ),
				'selector' => '{{ELEMENT}} .zb-el-tabs-content',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Tabs/editor', 'js' ) );
		wp_enqueue_script( 'zb-element-tabs', Plugin::instance()->scripts->get_script_url( 'elements/Tabs/frontend', 'js' ), [], Plugin::instance()->get_version(), true );

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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Tabs/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$tabs               = $this->get_children();
		$tab_links          = [];
		$title_attributes   = $this->render_attributes->get_attributes_as_string( 'inner_content_styles_title' );
		$content_attributes = $this->render_attributes->get_attributes_as_string( 'inner_content_styles_content', [ 'class' => 'zb-el-tabs-content' ] );

		foreach ( $tabs as $key => $tab_data ) {
			$title    = isset( $tab_data['options']['title'] ) ? $tab_data['options']['title'] : '';
			$active   = $key === 0 ? 'zb-el-tabs-nav--active' : '';
			$selected = $key === 0 ? 'aria-selected="true"' : '';
			$tabindex = $key === 0 ? 'tabindex="0"' : 'tabindex="-1"';

			$tab_links[] = sprintf( '<li role="tab" %s %s class="zb-el-tabs-nav-title %s" %s>%s</li>', $selected, $tabindex, esc_attr( $active ), $title_attributes, wp_kses_post( $title ) );
		} ?>
		<ul class="zb-el-tabs-nav" role="tablist">
			<?php
				// All output is already escaped
				echo implode( '', $tab_links ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		</ul>
		<div <?php echo $content_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php
			foreach ( $tabs as $index => $element_data ) {
				Plugin::$instance->renderer->render_element(
					$element_data,
					[
						'active' => $index === 0,
					]
				);
			}
			?>
		</div>
		<?php
	}
}
