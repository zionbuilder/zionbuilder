<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;

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
	private $registered_areas      = [];
	private $instantiated_elements = [];

	public function __construct() {
	}


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
	 * @param string $area_id
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
		foreach ( $children as $element_id => $element_data ) {
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
			// Check for repeater data
			$element_intance = $this->instantiated_elements[$element_data['uid']];

			// Check to see if the element is also a provider
			if ( $element_intance->is_repeater_provider() ) {
				$repeater_provider_config = $element_intance->get_repeater_provider_config();

				// Setting query
				\ZionBuilderPro\Repeater::set_active_query( $repeater_provider_config, $element_intance );
			}

			// Loop through repeater items
			if ( $element_intance->is_repeater_consumer() ) {
				\ZionBuilderPro\Repeater::set_consumer( $element_intance->get_repeater_consumer_config() );
				$consumer_items = \ZionBuilderPro\Repeater::get_consumer_items();

				if ( is_array( $consumer_items ) ) {
					foreach ( $consumer_items as $index => $consumer_item['data'] ) {
						\ZionBuilderPro\Repeater::iterate();
						if ( $index === 0 ) {
							$element_intance->is_repeater_main = true;
							$element_intance->render_element( $extra_data );
						} else {
							$old_uid        = $element_intance->uid;
							$new_uid        = sprintf( '%s_%s', $old_uid, $index );
							$clone_instance = $element_intance->get_clone(
								[
									'uid' => $new_uid,
								]
							);

							$clone_instance->is_repeater_item = true;
							$clone_instance->render_element( $extra_data );
						}

						\ZionBuilderPro\Repeater::next();
					}
				}

				\ZionBuilderPro\Repeater::reset_index();

			} else {
				$this->instantiated_elements[$element_data['uid']]->render_element( $extra_data );
			}

			if ( $element_intance->is_repeater_provider() ) {
				\ZionBuilderPro\Repeater::reset_query();
			}
		}
	}

	public function replace_uids( $data, $index ) {

	}

	public function render_area( $area_id ) {
		echo '<div class="zb zb-area-' . esc_attr( $area_id ) . '">';
		$this->render_children( $this->get_content_for_area( $area_id ) );
		echo '</div>';
	}

	/**
	 * Register Element Instance
	 *
	 * Registers the element instances so we can use them
	 *
	 * @param array $element_data
	 *
	 * @return void
	 */
	public function register_element_instance( $element_data ) {
		$element_instance_with_data = Plugin::$instance->elements_manager->get_element_instance_with_data( $element_data );
		// Don't proceed if we do not have an element instance
		if ( false === $element_instance_with_data || ! isset( $element_data['uid'] ) ) {
			return;
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
	 * @param string $area_name
	 * @param array  $area_template_data
	 *
	 * @return void
	 */
	public function register_area( $area_name, $area_template_data ) {
		$this->registered_areas[$area_name] = $area_template_data;
		$this->prepare_area_for_render( $area_name );
	}

	/**
	 * Get Elements Instances
	 *
	 * Returns an arary containing all element instances for the current page
	 *
	 * @return array
	 */
	public function get_elements_instances() {
		return $this->instantiated_elements;
	}

	public function get_content( $area_id = 'content' ) {
		ob_start();
		$this->render_area( $area_id );
		return ob_get_clean();
	}
}
