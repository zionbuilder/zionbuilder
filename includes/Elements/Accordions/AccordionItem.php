<?php

namespace ZionBuilder\Elements;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class AccordionItem extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'accordion_item';
	}


	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Accordion', 'zionbuilder' );
	}


	/**
	 * Is child
	 *
	 * Will register the current element as a child of another
	 *
	 * Child elements are not visible in add elements popup and cannot be
	 * interacted with them directly
	 */
	public function is_child() {
		return true;
	}

	/**
	 * Registers the element options
	 *
	 * @param Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'title',
			[
				'type'    => 'text',
				'title'   => esc_html__( 'Title', 'zionbuilder' ),
				'default' => esc_html__( 'Accordion title', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'content',
			[
				'type'    => 'editor',
				'title'   => esc_html__( 'Content', 'zionbuilder' ),
				'default' => __( 'Accordion content', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'active_by_default',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Active by default?', 'zionbuilder' ),
			]
		);
	}

	/**
	 * Sets wrapper css classes
	 *
	 * @param Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		$active_by_default = $options->get_value( 'active_by_default' );
		$this->render_attributes->add( 'wrapper', 'class', 'zb-el-accordions-accordionWrapper' );

		if ( $active_by_default ) {
			$this->render_attributes->add( 'wrapper', 'class', 'zb-el-accordions--active' );
		}
	}

	/**
	 * Render
	 *
	 * Will render the element based on options
	 *
	 * @param Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$title   = $options->get_value( 'title' );
		$content = $options->get_value( 'content' ); ?>

		<div class="zb-el-accordions-accordionTitle" tabindex="0" role="button">
			<?php echo wp_kses_post( $title ); ?>
			<span class="zb-el-accordions-accordionIcon"></span>
		</div>
		<div class="zb-el-accordions-accordionContent">

			<div
				class="zb-el-accordions-accordionContent__inner"
			><?php echo wp_kses_post( $content ); ?></div>

		</div>
		<?php
	}
}
