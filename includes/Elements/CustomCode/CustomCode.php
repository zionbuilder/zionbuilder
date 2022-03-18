<?php

namespace ZionBuilder\Elements\CustomCode;

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
class CustomCode extends Element {
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
		return __( 'Custom Code', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'html', 'css', 'javascript', 'js', 'code', 'custom', 'shortcode', 'shrt', 'txt', 'markup' ];
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
		$custom_html_group = $options->add_group(
			'custom_html',
			[
				'type'  => 'panel_accordion',
				'title' => __( 'Custom HTML/CSS/JavaScript', 'zionbuilder' ),
			]
		);

		$custom_html_group->add_option(
			'content',
			[
				'type'        => 'code',
				'description' => __( 'Using this option you can enter you own custom HTML code. If you plan on adding CSS or JavaScript, wrap the codes into <style type="text/css">...</style> respectively <script>...</script> . Please make sure your JS code is fully functional as it might break the entire page!!', 'zionbuilder' ),
				'title'       => esc_html__( 'Custom html', 'zionbuilder' ),
				'mode'        => 'htmlmixed',
				'default'     => esc_html__( '// Your custom HTML here', 'zionbuilder' ),
			]
		);

		$custom_php_group = $options->add_group(
			'custom_php',
			[
				'type'  => 'panel_accordion',
				'title' => __( 'Custom PHP', 'zionbuilder' ),
			]
		);

		$custom_php_group->add_option(
			'upgrade_to_pro',
			[
				'type'                => 'upgrade_to_pro',
				'message_title'       => esc_html__( 'Meet Custom PHP', 'zionbuilder' ),
				'message_description' => esc_html__( 'With custom PHP you can add additional logic to your page', 'zionbuilder' ),
				'info_text'           => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),
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
