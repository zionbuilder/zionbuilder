<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;
use ZionBuilder\Elements\Element;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Frontend
 *
 * @package ZionBuilder
 */
class Renderer {
	/**
	 * Holds a reference to the registered post areas that needs to render on the current page
	 *
	 * @var array
	 */
	private $registered_areas = [];

	/**
	 * Holds a list of Zion instantiated elements
	 *
	 * @var array
	 */
	private $instantiated_elements = [];


	/**
	 * Get Registered Areas
	 *
	 * Returns an array containing all registered areas and their data
	 *
	 * @return array
	 */
	public function get_registered_areas() {
		return $this->registered_areas;
	}

	/**
	 * Get Content For Area
	 *
	 * Returns the area content data for the given area id
	 *
	 * @param integer $area_id
	 *
	 * @return array
	 */
	public function get_content_for_area( $area_id ) {
		if ( ! empty( $this->registered_areas[$area_id] ) && is_array( $this->registered_areas[$area_id] ) ) {
			return $this->registered_areas[$area_id];
		}

		return [];
	}

	/**
	 * Render Children
	 *
	 * Will loop trough all provided elements and will render them
	 *
	 * @param array $children Array containing all children elements
	 *
	 * @return void
	 */
	public function render_children( $children ) {
		foreach ( $children as $element_data ) {
			$this->render_element( $element_data );
		}
	}

	/**
	 * Render a single element
	 *
	 * @param array $element_data
	 * @param array $extra_data
	 *
	 * @return void
	 */
	public function render_element( $element_data, $extra_data = [] ) {
		if ( isset( $element_data['uid'] ) && isset( $this->instantiated_elements[$element_data['uid']] ) ) {
			$this->instantiated_elements[$element_data['uid']]->render_element( $extra_data );
		}
	}

	/**
	 * Register Element Instance
	 *
	 * Registers the element instances so we can use them
	 *
	 * @param array $element_data
	 *
	 * @return Element|boolean Returns the element or false if the element could not be instantiated
	 */
	public function register_element_instance( $element_data ) {
		$element_instance_with_data = Plugin::$instance->elements_manager->get_element_instance_with_data( $element_data );

		// Don't proceed if we do not have an element instance
		if ( false === $element_instance_with_data || ! isset( $element_data['uid'] ) ) {
			return false;
		}

		$this->instantiated_elements[$element_data['uid']] = $element_instance_with_data;

		// Check if element has children
		$element_children = $element_instance_with_data->get_children();

		// Instantiate all children elements
		if ( ! empty( $element_children ) && is_array( $element_children ) ) {
			foreach ( $element_children as $child_element_data ) {
				$this->register_element_instance( $child_element_data );
			}
		}

		return $element_instance_with_data;
	}

	/**
	 * Prepare content for render
	 *
	 * Instantiate all elements that should be rendered on the current page
	 *
	 * @return void
	 */
	public function prepare_areas_for_render() {
		foreach ( $this->get_registered_areas() as $area_name => $area_template_data ) {
			$this->prepare_area_for_render( $area_name );
		}
	}


	/**
	 * Registers all elements required for a given post id/area
	 *
	 * @param integer $area_id The post id for which we need to register the element instances
	 *
	 * @return void
	 */
	public function prepare_area_for_render( $area_id ) {
		if ( ! empty( $this->registered_areas[$area_id] ) ) {
			foreach ( $this->registered_areas[$area_id] as $element_data ) {
				$this->register_element_instance( $element_data );
			}
		}
	}

	/**
	 * Register Area
	 *
	 * Register a new area of elements
	 *
	 * @param integer $area_name
	 *
	 * @param array  $area_template_data
	 *
	 * @return void
	 */
	public function register_area( $area_name, $area_template_data ) {
		$this->registered_areas[$area_name] = $area_template_data;
		$this->prepare_area_for_render( $area_name );
	}


	/**
	 * Returns an instance of an element
	 *
	 * @param string $element_uid
	 *
	 * @return Element|false
	 */
	public function get_element_instance( $element_uid ) {
		if ( isset( $this->instantiated_elements[$element_uid] ) ) {
			return $this->instantiated_elements[$element_uid];
		}

		return false;
	}

	/**
	 * Will render a post ID
	 *
	 * @param integer $post_id The post id for which you want to render the elements
	 * @param array $custom_content A different content list that needs to be rendered
	 *
	 * @return void
	 */
	public function render_area( $post_id, $custom_content = null ) {
		$area_class       = sprintf( 'zb-area-%s', $post_id );
		$classes          = apply_filters( 'zionbuilder/single/area_class', [ 'zb', $area_class ], $post_id );
		$area_content     = $custom_content ? $custom_content : $this->get_content_for_area( $post_id );
		$disable_wrappers = Settings::get_value( 'performance.disable_area_wrappers', false ) === true;

		if ( ! $disable_wrappers ) {
			echo '<div class="' . implode( ' ', array_map( 'esc_attr', $classes ) ) . '">';
		}
			$this->render_children( $area_content );

		if ( ! $disable_wrappers ) {
			echo '</div>';
		}
	}


	/**
	 * Will return the content of a post
	 *
	 * @param integer $post_id The post id for which you want to render the elements
	 * @param array $custom_content A different content list that needs to be rendered
	 *
	 * @return string
	 */
	public function get_content( $post_id, $custom_content = null ) {
		ob_start();
		$this->render_area( $post_id, $custom_content );
		return ob_get_clean();
	}
}
