<?php

namespace ZionBuilder\Elements\AnchorPoint;

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
class AnchorPoint extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'anchor_point';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Anchor point', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'link', 'scroll to', 'scrollto', 'id', 'anchor', 'point', 'dash', 'tag', 'hashtag' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-anchor-point';
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
			'id',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Id', 'zionbuilder' ),
				'description' => esc_html__( 'Please enter an id for this anchor point. You can use this #id for an anchor href.', 'zionbuilder' ),
				'default'     => '%%ELEMENT_UID%%',
				'placeholder' => '%%ELEMENT_UID%%',
				'sync'        => '_advanced_options._element_id',
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
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/AnchorPoint/editor', 'js' ) );
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
		$this->enqueue_editor_style( Utils::get_file_url( 'dist/elements/AnchorPoint/editor.css' ) );
	}
	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return string
	 */
	public function render( $options ) {
		return '';
	}
}
