<?php

namespace ZionBuilder\Elements\Accordions;

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
class Accordions extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'accordions';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Accordions', 'zionbuilder' );
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
		return [ 'accordions', 'folding', 'dropdown', 'drop-down', 'drop down', 'expand', 'navigation', 'tabs', 'panels' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-accordion';
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
			'items',
			[
				'type'         => 'child_adder',
				'title'        => __( 'Accordions', 'zionbuilder' ),
				'child_type'   => 'accordion_item',
				'item_name'    => 'title',
				'min'          => 1,
				'add_template' => [
					'element_type' => 'accordion_item',
					'options'      => [
						'title' => __( 'Accordion', 'zionbuilder' ),
					],
				],
				'default'      => [
					[
						'element_type' => 'accordion_item',
						'options'      => [
							'title' => sprintf( '%s 1', __( 'Accordion', 'zionbuilder' ) ),
						],
					],
					[
						'element_type' => 'accordion_item',
						'options'      => [
							'title' => sprintf( '%s 2', __( 'Accordion', 'zionbuilder' ) ),
						],
					],
				],
			]
		);

		$options->add_option(
			'type',
			[
				'type'             => 'select',
				'title'            => __( 'Type', 'zionbuilder' ),
				'default'          => 'accordion',
				'options'          => [
					[
						'name' => __( 'Accordion', 'zionbuilder' ),
						'id'   => 'accordion',
					],
					[
						'name' => __( 'Toggle', 'zionbuilder' ),
						'id'   => 'toggle',
					],
				],
				'render_attribute' => [
					[
						'attribute' => 'data-accordion-type',
					],
				],
			]
		);

		$options->add_option(
			'title_tag',
			[
				'type'        => 'select',
				'description' => esc_html__( 'Select the HTML tag to use for the title. If you want to add a custom tag, make sure to only use letters and numbers', 'zionbuilder' ),
				'title'       => esc_html__( 'HTML tag for titles', 'zionbuilder' ),
				'default'     => 'div',
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
			'single_accordion_styles',
			[
				'title'                   => esc_html__( 'Single accordion wrapper', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zb-el-accordions-accordionWrapper',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_title',
			[
				'title'                   => esc_html__( 'Title styles', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zb-el-accordions-accordionTitle',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'active_accordion_title',
			[
				'title'                   => esc_html__( 'Active accordion title styles', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zb-el-accordions--active .zb-el-accordions-accordionTitle',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'inner_content_styles_content',
			[
				'title'                   => esc_html__( 'Content styles', 'zionbuilder' ),
				'selector'                => '{{ELEMENT}} .zb-el-accordions-accordionContent',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Accordions/editor', 'js' ) );
		wp_enqueue_script( 'zb-element-accordions', Plugin::instance()->scripts->get_script_url( 'elements/Accordions/frontend', 'js' ), [], Plugin::instance()->get_version(), true );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Accordions/frontend.css' ) );
	}

	/**
	 * Render
	 *
	 * Will render the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$this->provide( 'accordionsElement', $this );

		$this->render_children();
	}
}
