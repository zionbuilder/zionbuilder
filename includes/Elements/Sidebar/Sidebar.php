<?php

namespace ZionBuilder\Elements\Sidebar;

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
class Sidebar extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'sidebar';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Sidebar', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'layout';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'sidebar', 'lateral', 'bar', 'vertical', 'side' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-sidebar';
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
			'sidebar_id',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Select sidebar', 'zionbuilder' ),
				'placeholder' => esc_html__( 'Select sidebar', 'zionbuilder' ),
				'options'     => $this->get_sidebars(),
			]
		);
	}

	/**
	 * Get sidebars
	 *
	 * Get a list of sidebars registered on current site
	 *
	 * @return array
	 */
	private function get_sidebars() {
		global $wp_registered_sidebars;

		$sidebar_options = [];

		if ( ! $wp_registered_sidebars ) {
			$sidebar_options[''] = __( 'No sidebars registered', 'zionbuilder' );
		} else {
			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$sidebar_options[] = [
					'name' => $sidebar['name'],
					'id'   => $sidebar_id,
				];
			}
		}

		return $sidebar_options;
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$sidebar_id = $options->get_value( 'sidebar_id' );

		if ( empty( $sidebar_id ) ) {
			esc_html_e( 'No sidebar selected', 'zionbuilder' );
		} else {
			dynamic_sidebar( $sidebar_id );
		}
	}
}
