<?php

namespace ZionBuilder\Elements\Accordions;

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
	 *
	 * @return boolean True in case this is a child element
	 */
	public function is_child() {
		return true;
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
				'default' => esc_html__( 'Accordion title', 'zionbuilder' ),
				'dynamic' => [
					'enabled' => true,
				],
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
	 * @param \ZionBuilder\Options\Options $options
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

	public function get_sortable_content_orientation() {
		return 'vertical';
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
		$accordions_element = $this->inject( 'accordionsElement' );
		$title              = $options->get_value( 'title' );
		$content            = $options->get_value( 'content' );
		$title_classes      = '';
		$content_classes    = '';

		if ( $accordions_element instanceof Element ) {
			$title_classes   = $accordions_element->get_style_classes_as_string( 'inner_content_styles_title' );
			$content_classes = $accordions_element->get_style_classes_as_string( 'inner_content_styles_content' );
		}

		?>

		<div class="zb-el-accordions-accordionTitle <?php echo esc_attr( $title_classes ); ?>" tabindex="0" role="button">
			<?php echo wp_kses_post( $title ); ?>
			<span class="zb-el-accordions-accordionIcon"></span>
		</div>
		<div class="zb-el-accordions-accordionContent <?php echo esc_attr( $content_classes ); ?>">

			<div
				class="zb-el-accordions-accordionContent__inner"
			><?php echo wp_kses_post( $content ); ?></div>

		</div>
		<?php
	}
}
