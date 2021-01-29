<?php

namespace ZionBuilder\Elements\Tabs;

use ZionBuilder\Elements\Element;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class TabsItem extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'tabs_item';
	}

	/**
	 * Is child
	 *
	 * Will register the current element as a child of another
	 *
	 * Child elements are not visible in add elements popup and cannot be
	 * interacted with them directly
	 *
	 * @return boolean True if the element is a child of another element
	 */
	public function is_child() {
		return true;
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Tab Item', 'zionbuilder' );
	}

	public function get_sortable_content_orientation() {
		return 'vertical';
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
			'title',
			[
				'type'    => 'text',
				'title'   => esc_html__( 'Title', 'zionbuilder' ),
				'default' => esc_html__( 'Tab title', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'content',
			[
				'type'    => 'editor',
				'title'   => esc_html__( 'Content', 'zionbuilder' ),
				'default' => __( 'Tab content', 'zionbuilder' ),
			]
		);
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		if ( $this->extra_render_data['active'] ) {
			$this->render_attributes->add( 'wrapper', 'class', 'zb-el-tabs-nav--active' );
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
		echo wp_kses_post( $options->get_value( 'content' ) );
	}
}
