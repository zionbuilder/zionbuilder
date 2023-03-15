<?php

namespace ZionBuilder\Elements\Link;

use ZionBuilder\Elements\Element;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Link
 *
 * @package ZionBuilder\Elements
 */
class Link extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_link';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Link', 'zionbuilder' );
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-text';
	}

	public function get_wrapper_tag( $options ) {
		return 'a';
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
			'content',
			[
				'type'        => 'text',
				'description' => esc_html__( 'Set the desired content for this element', 'zionbuilder' ),
				'title'       => esc_html__( 'Content', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Enter text', 'zionbuilder' ),
				'default'     => __( 'Just a sample text.', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'link',
			[
				'type'        => 'link',
				'title'       => __( 'Link', 'zionbuilder' ),
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/Link/editor', 'js' ) );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		// Add the grid column class
		$this->render_attributes->add( 'wrapper', 'class', 'zb-column' );
		$link = $options->get_value( 'link', false );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'wrapper', $link );
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
		$link = $options->get_value( 'link', false );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'wrapper', $link );
		}

		echo $options->get_value( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
