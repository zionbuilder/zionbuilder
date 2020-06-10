<?php

namespace ZionBuilder\Elements\CustomHtml;

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
class CustomHtml extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'custom_html';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Custom HTML', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'html', 'css', 'javascript' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-code';
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_script(), enqueue_element_script() functions
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/CustomHtml/editor.js' ) );
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
				'type'        => 'code',
				'description' => esc_html__( 'Using this option you can enter you own custom HTML code. If you plan on adding CSS or JavaScript, wrap the codes into <style type="text/css">...</style> respectively <script>...</script> . Please make sure your JS code is fully functional as it might break the entire page!!', 'zionbuilder' ),
				'title'       => esc_html__( 'Custom html', 'zionbuilder' ),
				'mode'        => 'htmlmixed',
				'default'     => esc_html__( '// Your custom HTML here', 'zionbuilder' ),
			]
		);
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		// We won't escape this as this element accepts user generated content
		echo $options->get_value( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
