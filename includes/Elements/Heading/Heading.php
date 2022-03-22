<?php

namespace ZionBuilder\Elements\Heading;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Heading
 *
 * @package ZionBuilder\Elements
 */
class Heading extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_heading';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Heading', 'zionbuilder' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'text', 'heading', 'title', 'paragraph', 'sentence', 'caption' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-heading';
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
				'type'        => 'editor',
				'description' => 'This is the element content',
				'title'       => __( 'Content', 'zionbuilder' ),
				'default'     => esc_html__( 'Just a sample text from heading element.', 'zionbuilder' ),
				'dynamic'     => [
					'enabled' => true,
				],
			]
		);

		$options->add_option(
			'link',
			[
				'type'        => 'link',
				'description' => 'This is the element content',
				'title'       => __( 'Link', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'tag',
			[
				'type'        => 'select',
				'default'     => 'h1',
				'title'       => __( 'HTML tag', 'zionbuilder' ),
				'description' => __( 'Set the desired heading tag.', 'zionbuilder' ),
				'options'     => [
					[
						'name' => __( 'h1', 'zionbuilder' ),
						'id'   => 'h1',
					],
					[
						'name' => __( 'h2', 'zionbuilder' ),
						'id'   => 'h2',
					],
					[
						'name' => __( 'h3', 'zionbuilder' ),
						'id'   => 'h3',
					],
					[
						'name' => __( 'h4', 'zionbuilder' ),
						'id'   => 'h4',
					],
					[
						'name' => __( 'h5', 'zionbuilder' ),
						'id'   => 'h5',
					],
					[
						'name' => __( 'h6', 'zionbuilder' ),
						'id'   => 'h6',
					],
					[
						'name' => __( 'p', 'zionbuilder' ),
						'id'   => 'p',
					],
					[
						'name' => __( 'span', 'zionbuilder' ),
						'id'   => 'span',
					],
					[
						'name' => __( 'div', 'zionbuilder' ),
						'id'   => 'div',
					],
				],
			]
		);

		$options->add_option(
			'align',
			[
				'type'                    => 'text_align',
				'title'                   => __( 'Align', 'zionbuilder' ),
				'description'             => __( 'Select the desired alignment.', 'zionbuilder' ),
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.text-align',
				'show_responsive_buttons' => true,
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
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/Heading/editor.js' ) );
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		$tag = $options->get_value( 'tag', 'h1' );

		$this->set_wrapper_tag( $tag );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$content = $options->get_value( 'content', esc_html__( 'Just a sample text from heading element.', 'zionbuilder' ) );
		$link    = $options->get_value( 'link', false );

		if ( ! empty( $link['link'] ) ) {
			$this->attach_link_attributes( 'link', $link );
			$this->render_tag( 'a', 'link', $content );
		} else {
			echo wp_kses_post( $content );
		}
	}
}
