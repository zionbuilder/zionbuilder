<?php

namespace ZionBuilder\Elements;

use ZionBuilder\Utils;
use ZionBuilder\Elements\Widget\Widget;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Manager
 *
 * @package ZionBuilder\Elements
 */
class Manager {
	/**
	 * Holds a reference to all elements
	 */
	private $elements = null;

	/**
	 * Register Elements
	 *
	 * Registers default elements and fires a custom action that other can use to register their own elements
	 *
	 * @return void
	 */
	private function register_elements() {
		// Register default elements
		$this->register_default_elements();

		/*
		 * Allow others to register their own elements
		 */
		do_action( 'zionbuilder/elements_manager/register_elements', $this );
	}


	/**
	 * Register default elements
	 */
	private function register_default_elements() {
		/**
		 * The list of all the plugin's default elements
		 */
		$default_elements = [
			'Text/Text',
			'Section/Section',
			'Column/Column',
			'CustomCode/CustomCode',
			'GoogleMaps/GoogleMaps',
			'Counter/Counter',
			'ProgressBars/ProgressBars',
			'ImageSlider/ImageSlider',
			'AnchorPoint/AnchorPoint',
			'IconList/IconList',
			'Testimonial/Testimonial',
			'Alert/Alert',
			'Shortcode/Shortcode',
			'Sidebar/Sidebar',
			'Soundcloud/Soundcloud',
			'PricingBox/PricingBox',
			'Tabs/Tabs',
			'Tabs/TabsItem',
			'Accordions/Accordions',
			'Accordions/AccordionItem',
			'ImageBox/ImageBox',
			'Image/Image',
			'Icon/Icon',
			'IconBox/IconBox',
			'Gallery/Gallery',
			'Heading/Heading',
			'Video/Video',
			'Button/Button',
			'Separator/Separator',
			'Container/Container',
			'Link/Link',
		];

		foreach ( $default_elements as $element_path ) {
			$file_path = Utils::get_file_path( 'includes/Elements/' . $element_path . '.php' );

			if ( is_file( $file_path ) ) {
				include $file_path;

				// Normalize class name
				$class_name = str_replace( '/', '\\', $element_path );
				$class_name = __NAMESPACE__ . '\\' . $class_name;

				$this->register_element( new $class_name() );
			}
		}

		// Register WordPress widgets
		$this->register_wordpress_widgets();
	}

	private function register_wordpress_widgets() {
		global $wp_widget_factory;

		foreach ( $wp_widget_factory->widgets as $widget_id => $widget ) {
			$widget_instance = new Widget();
			$widget_instance->set_widget_id( $widget_id );

			$this->register_element(
				$widget_instance
			);
		}
	}

	/**
	 * Get elements
	 *
	 * Returns an array containing all registered elements
	 *
	 * @return array
	 */
	public function get_elements() {
		if ( null === $this->elements ) {
			$this->register_elements();
		}

		return $this->elements;
	}


	/**
	 * Return an instance of the element
	 *
	 * @param string $element_id The element id to retrieve
	 */
	public function get_element( $element_id ) {
		$this->get_elements();

		if ( ! empty( $this->elements[$element_id] ) ) {
			return $this->elements[$element_id];
		}

		return false;
	}

	/**
	 * Get Elements Config For Editor
	 *
	 * Returns the elements config that will be used by JavaScript
	 *
	 * @return array
	 */
	public function get_elements_config_for_editor() {
		$elements_config = [];

		foreach ( $this->get_elements() as $element_instance ) {
			$elements_config[] = $element_instance->internal_get_config_for_editor();
		}

		return $elements_config;
	}

	/**
	 * Register Element
	 *
	 * Adds a new element to the registered elements array
	 *
	 * @param Element $element_class
	 *
	 * @return Manager
	 */
	public function register_element( Element $element_class ) {
		$this->elements[$element_class->get_type()] = $element_class;

		// Allow chaining
		return $this;
	}

	/**
	 * Unregister Element
	 *
	 * @param string $element_id The element id that needs to be unregistered
	 *
	 * @return boolean True/False depending if the element was unregistered or not
	 */
	public function unregister_element( $element_id ) {
		if ( isset( $this->elements[$element_id] ) ) {
			unset( $this->elements[$element_id] );
			return true;
		}

		return false;
	}

	/**
	 * Get Elements Categories
	 *
	 * Returns all elements categories
	 *
	 * @return array
	 */
	public function get_elements_categories() {
		$categories = [
			[
				'id'       => 'layout',
				'name'     => __( 'Layout', 'zionbuilder' ),
				'priority' => 10,
			],
			[
				'id'       => 'content',
				'name'     => __( 'Content', 'zionbuilder' ),
				'priority' => 20,
			],
			[
				'id'       => 'media',
				'name'     => __( 'Media', 'zionbuilder' ),
				'priority' => 30,
			],
			[
				'id'       => 'external',
				'name'     => __( 'External', 'zionbuilder' ),
				'priority' => 40,
			],
			[
				'id'       => 'widgets',
				'name'     => __( 'WordPress Widgets', 'zionbuilder' ),
				'priority' => 50,
			],
		];

		return apply_filters( 'zionbuilder/elements/categories', $categories );
	}

	/**
	 * Get Element Instance
	 *
	 * Returns an instance of the requested barebone element instance
	 *
	 * @param string $element_id The element id for which we need to return the instance
	 *
	 * @return boolean|Element Element instance or false if the element was not registered
	 */
	public function get_element_instance( $element_id ) {
		$this->get_elements();

		if ( ! empty( $this->elements[$element_id] ) ) {
			return $this->elements[$element_id];
		}

		return false;
	}

	/**
	 * Will return an instance of the requested element and data
	 *
	 * @param array $data
	 *
	 * @return boolean|Element Element instance with data or false in case the element instance couldn't be created
	 */
	public function get_element_instance_with_data( $data ) {
		$element_instance = $this->get_element_instance( $data['element_type'] );

		if ( false !== $element_instance ) {
			$element_class_name = $element_instance->get_class_name();
			$instance_with_data = new $element_class_name( $data );
			return $instance_with_data;
		}

		return false;
	}
}
