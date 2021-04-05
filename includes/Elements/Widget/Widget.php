<?php

namespace ZionBuilder\Elements\Widget;

use ZionBuilder\Elements\Element;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Widget
 *
 * @package ZionBuilder\Elements\Widget
 */
class Widget extends Element {
	/**
	 * Holds the name of a WP_Widget class
	 *
	 * @var string
	 */
	private $widget_id = null;

	/**
	 * Holds the reference to the instance of a WP_Widget class
	 *
	 * @var array $data
	 */
	private $widget_instance = null;

	public function on_before_init( $data = [] ) {
		// Get the widget id from data
		if ( ! isset( $data['widget_id'] ) ) {
			return;
		}

		$this->set_widget_id( $data['widget_id'] );
	}


	public function set_widget_id( $widget_id ) {
		$this->widget_id = $widget_id;
	}

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'zion_wp_widget_' . $this->widget_id;
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		global $wp_widget_factory;

		return $wp_widget_factory->widgets[$this->widget_id]->name;
	}

	public function get_category() {
		return 'widgets';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'wordpress', 'widget' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-wp';
	}

	public function extra_data() {
		return [
			'widget_id' => $this->widget_id,
		];
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$widget_instance = $this->get_widget_instance();

		$options->add_option(
			'widget_settings',
			[
				'type'         => 'wp_widget',
				'element_type' => $widget_instance->id_base,
			]
		);
	}

	/**
	 * @return \WP_Widget|null
	 */
	private function get_widget_instance() {
		if ( is_null( $this->widget_instance ) && $this->widget_id ) {
			global $wp_widget_factory;

			if ( isset( $wp_widget_factory->widgets[$this->widget_id] ) ) {
				$this->widget_instance = $wp_widget_factory->widgets[$this->widget_id];
				$this->widget_instance->_set( 'ZION_BUILDER_PLACEHOLDER_ID' );
			} elseif ( class_exists( $this->widget_id ) ) {
				$this->widget_instance = new $this->widget_id();
				$this->widget_instance->_set( 'ZION_BUILDER_PLACEHOLDER_ID' );
			}
		}

		return $this->widget_instance;
	}


	/**
	 * Render the widget form
	 *
	 * @return string
	 */
	public function dynamic_options_form() {
		$widget_settings = $this->options->get_value( 'widget_settings' );
		$widget_instance = $this->get_widget_instance();

		//#! Make sure we have an instance
		$widget_id = ( $widget_instance ? $widget_instance->id_base : '' );

		ob_start();
		echo '<div class="widget-inside media-widget-control"><div class="form wp-core-ui">';
		echo '<input type="hidden" class="id_base" value="' . esc_attr( $widget_id ) . '" />';
		echo '<input type="hidden" class="widget-id" value="widget-' . esc_attr( strval( time() ) ) . '" />';
		echo '<div class="widget-content">';
		if ( $widget_instance ) {
			$widget_instance->form( $widget_settings );

			// This action is documented in wp-includes\class-wp-widget.php
			do_action( 'in_widget_form', $widget_instance, null, $widget_settings );
		}
		echo '</div></div></div>';

		return ob_get_clean();
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		the_widget(
			$this->widget_id,
			$options->get_value( 'widget_settings' ),
			[
				'widget_id' => 'znpb_widget' . $this->get_uid(),
			]
		);
	}
}
