<?php

namespace ZionBuilder\Options;

use ZionBuilder\RenderAttributes;
use ZionBuilder\CustomCSS;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Options
 *
 * Will provide an interface to easily manage builder options
 * for elements, page settings, etc
 *
 * @package ZionBuilder
 */
class Options extends Stack {
	private $triggered_injection = null;

	/**
	 * Holds a reference to all elements schemas
	 */
	private static $schemas = [];

	/**
	 * Holds a reference to the current schema id
	 */
	private $schema_id = null;

	/**
	 * Holds a reference to the options model/values
	 */
	private $model = [];

	private $css_selector = null;

	/**
	 * Holds the custom css that will be applied to an element
	 * @var CustomCSS|null
	 */
	private $custom_css = null;

	/**
	 * @var RenderAttributes|null
	 */
	private $render_attributes = null;

	/**
	 * Class constructor
	 *
	 * @var string $id The id for the current stack
	 * @var array $options The options schema for the current stack
	 *
	 */
	public function __construct( $id, $options = [] ) {
		$this->schema_id = $id;

		// Save the schema for this schema
		$this->register_schema( $id, $options );
	}

	/**
	 * Trigger injection
	 *
	 * Will fire an event so that other developers can modify the schema
	 */
	public function trigger_injection() {
		/*
		 * Allow others to add their own options
		 */
		do_action( $this->schema_id, $this );
	}

	/**
	 * Register schema
	 *
	 * Will register an option schema to the option schema stach
	 *
	 * @var string $id The id for the current stack. This must be unique
	 * @var array $options An array containing the options that will be added to stack
	 */
	private function register_schema( $id, $options ) {
		if ( ! isset( self::$schemas[$id] ) ) {
			self::$schemas[$id] = [];

			// Register options
			if ( ! empty( $options ) && is_array( $options ) ) {
				foreach ( $options as $option_id => $option_config ) {
					$this->add_option( $option_id, $option_config );
				}
			}
		}
	}

	/**
	 * Set the options model/values
	 *
	 * @param array $model The data model for the current stack
	 */
	public function set_model( $model = [] ) {
		$this->model = $model;
	}

	/**
	 * Get schema
	 *
	 * Returns the current schema
	 *
	 * @return array The schema stack
	 */
	public function get_schema() {
		if ( $this->triggered_injection === null ) {
			$this->triggered_injection = true;
			$this->trigger_injection();
		}

		return self::$schemas[$this->schema_id];
	}

	/**
	 * Add option
	 *
	 * Will add a new option to the stack
	 *
	 * @var string $option_id The unique id for the option
	 * @var array $option_config The option config
	 *
	 * @return Option the newly added option
	 */
	public function add_option( $option_id, $option_config = [] ) {
		return $this->add_to_stack( $option_id, $option_config, self::$schemas[$this->schema_id] );
	}

	/**
	 * Remove option
	 *
	 * Will remove an option from the stack
	 *
	 * @var string $option_id The id of the option you want to remove
	 *
	 * @return boolean True if the option was successfully removed, false on failure
	 */
	public function remove_option( $option_id ) {
		return $this->remove_from_stack( $option_id, self::$schemas[$this->schema_id] );
	}

	/**
	 * Replace option
	 *
	 * Will replace an option from the stack
	 *
	 * @var string $option_id The option id
	 * @var array $option_config The option config that should replace the option
	 *
	 * @return Option the new option that replaced the old one
	 */
	public function replace_option( $option_id, $option_config ) {
		return $this->add_option( $option_id, $option_config );
	}

	/**
	 * Get option
	 *
	 * Returns a single option from the stack
	 *
	 * @var mixed $option_path The path to the option you want to retrieve
	 *
	 * @return Option|null The requested option
	 */
	public function get_option( $option_path ) {
		$path_locations = explode( '.', $option_path );
		$paths_count    = count( $path_locations );
		$options_schema = $this->get_schema();
		$i              = 1;

		foreach ( $path_locations as $path_location ) {
			if ( ! array_key_exists( $path_location, $options_schema ) ) {
				return null;
			}

			if ( $i === $paths_count ) {
				return $options_schema[$path_location];
			}

			if ( ! isset( $options_schema[$path_location]->child_options ) ) {
				return null;
			}

			$options_schema = $options_schema[$path_location]->child_options;
			$i++;
		}

		return null;
	}

	/**
	 * Parse model
	 *
	 * Will set the base model and the element for which the model is attached to
	 *
	 * @param RenderAttributes $render_attributes
	 * @param CustomCSS $custom_css
	 */
	public function parse_data( RenderAttributes $render_attributes, CustomCSS $custom_css ) {
		$this->render_attributes = $render_attributes;
		$this->custom_css        = $custom_css;

		$this->parse_options( $this->get_schema(), $this->model );
	}


	/**
	 * Parse options
	 *
	 * Will loop through the options setting the active options, render attributes
	 * and custom css
	 *
	 * @var array $schema The options schema that will be parsed
	 * @var array $model The current saved values
	 * @var integer $index The index of the model values in case of repeater options
	 *
	 * @return void
	 */
	public function parse_options( $schema, &$model, $index = null ) {
		foreach ( $schema as $option_id => $option_schema ) {
			$passed_dependency = $this->check_dependency( $option_schema, $model );

			// Don't proceed if we didn't passed dependency
			if ( ! $passed_dependency ) {
				continue;
			}

			// Group options don't store the value so we need to look at childs
			if ( isset( $option_schema->is_layout ) && $option_schema->is_layout ) {
				if ( isset( $option_schema->child_options ) ) {
					$this->parse_options( $option_schema->child_options, $model );
				}
			} else {
				// Set the option value to model with fallback to default
				if ( isset( $model[$option_id] ) ) {

					// Check for render attributes
					if ( $this->render_attributes ) {
						$this->render_attributes->parse_options_schema( $option_schema, $model[$option_id], $index );
					}

					// check for custom css
					if ( $this->custom_css ) {
						$this->custom_css->parse_options_schema( $option_schema, $model[$option_id], $index );
					}
				} elseif ( isset( $option_schema->default ) ) {
					$model[$option_id] = $option_schema->default;
				}

				// Check for child content
				if ( isset( $option_schema->child_options ) ) {
					if ( $option_schema->type === 'repeater' ) {
						if ( ! empty( $model[$option_id] ) && is_array( $model[$option_id] ) ) {
							foreach ( $model[$option_id] as $index => &$option_value ) {
								$this->parse_options( $option_schema->child_options, $option_value, $index );
							}
						}
					} else {
						$saved_value = isset( $model[$option_id] ) ? $model[$option_id] : [];
						$this->parse_options( $option_schema->child_options, $saved_value );

						if ( ! empty( $saved_value ) ) {
							$model[$option_id] = $saved_value;
						}
					}
				}
			}
		}
	}

	/**
	 * Will check if a current option passes dependency
	 *
	 * @param Option $option_schema The option schema
	 * @param mixed $model The saved value
	 *
	 * @return boolean True in case the dependency passed
	 */
	public function check_dependency( $option_schema, $model ) {
		$passed_dependency = true;

		if ( isset( $option_schema->dependency ) ) {
			foreach ( $option_schema->dependency as $dependency_config ) {
				$passed_dependency = $this->check_single_dependency( $dependency_config, $model );

				if ( ! $passed_dependency ) {
					break;
				}
			}
		}

		return $passed_dependency;
	}

	/**
	 * Will check a single configuration if it passes dependency
	 *
	 * @param array $dependency_config The dependency configuration
	 * @param mixed $model The saved value
	 *
	 * @return boolean True in case the dependency passed
	 */
	public function check_single_dependency( $dependency_config, $model ) {
		$validation_type = isset( $dependency_config['type'] ) ? $dependency_config['type'] : 'includes';
		$value           = null;

		if ( isset( $dependency_config['option'] ) ) {
			$value = isset( $model[$dependency_config['option']] ) ? $model[$dependency_config['option']] : null;
		} else {
			if ( isset( $dependency_config['option_path'] ) ) {
				$value = $this->get_value_from_path( $dependency_config['option_path'] );
			}
		}

		if ( $validation_type === 'includes' && in_array( $value, $dependency_config['value'], true ) ) {
			return true;
		} else {
			if ( $validation_type === 'not_in' && ! in_array( $value, $dependency_config['value'], true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the current stack model
	 *
	 * @return array
	 */
	public function get_model() {
		return $this->model;
	}

	/**
	 * Returns a saved value from the model
	 *
	 * @param string $path The path to the requested option value
	 *
	 * @return mixed|null The saved value. In case the value is not saved, null is returned
	 */
	public function get_value_from_path( $path = '' ) {
		$path_locations = explode( '.', $path );
		$active_model   = $this->get_model();
		$paths_count    = count( $path_locations );
		$i              = 1;

		foreach ( $path_locations as $path_location ) {
			if ( ! array_key_exists( $path_location, $active_model ) ) {
				return null;
			}

			if ( $i === $paths_count ) {
				return $active_model[$path_location];
			}

			$active_model = $active_model[$path_location];
			$i++;
		}

		return null;
	}

	/**
	 * Get value
	 *
	 * Returns the value for an option if model is provided
	 *
	 * @param string $path          A dot separated string
	 * @param mixed $default_value The default value to return in case the option value is not found
	 * @return mixed|null
	 */
	public function get_value( $path, $default_value = null ) {
		$value = $this->get_value_from_path( $path );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		return $default_value;
	}

}
