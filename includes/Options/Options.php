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
	/**
	 * Flag to check if the filter was applied
	 *
	 * @var boolean True in case the filter was already applied
	 */
	private $triggered_injection = null;

	/**
	 * Holds a reference to all elements schemas
	 *
	 * @var array<string, mixed>
	 */
	private static $schemas = [];

	/**
	 * Holds a reference to the current schema id
	 *
	 * @var string The schema unique id
	 */
	private $schema_id = null;

	/**
	 * Holds a reference to the options model/values
	 *
	 * @var array<string, mixed> The saved values
	 */
	private $model = [];

	/**
	 * Holds the custom css that will be applied to an element
	 *
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
	 * @param mixed $id
	 * @param mixed $options
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
	 *
	 * @uses \do_action()
	 *
	 * @return void
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
	 * @param string               $id      The id for the current stack. This must be unique
	 * @param array<string, mixed> $options An array containing the options that will be added to stack
	 *
	 * @return void
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
	 * Checks to see if an option schema was already registered
	 *
	 * @param string $schema_id
	 * @return boolean
	 */
	public static function is_schema_registered( $schema_id ) {
		return isset( self::$schemas[$schema_id] );
	}

	/**
	 * Set the options model/values
	 *
	 * @param array<string, mixed> $model The data model for the current stack
	 *
	 * @return void
	 */
	public function set_model( $model = [] ) {
		$this->model = $model;
	}

	/**
	 * Sets the options model, custom css and render attributes instances
	 *
	 * @param array<string, mixed> $model
	 * @param RenderAttributes     $render_attributes
	 * @param CustomCSS            $custom_css
	 *
	 * @return void
	 */
	public function set_data( $model, RenderAttributes $render_attributes, CustomCSS $custom_css ) {
		$this->model             = $model;
		$this->render_attributes = $render_attributes;
		$this->custom_css        = $custom_css;
	}

	/**
	 * Get schema
	 *
	 * Returns the current schema
	 *
	 * @return Option[] The schema stack
	 */
	public function get_schema() {
		if ( $this->triggered_injection === null ) {
			$this->triggered_injection = true;
			$this->trigger_injection();
		}

		return self::$schemas[$this->schema_id];
	}

	/**
	 * Returns the list of options for the current schema
	 *
	 * @return Option[] The schema stack
	 */
	public function &get_stack() {
		return self::$schemas[$this->schema_id];
	}


	/**
	 * Parse model
	 *
	 * Will set the base model and the element for which the model is attached to
	 *
	 * @return void
	 */
	public function parse_data() {
		$model = apply_filters( 'zionbuilder/options/model_parse', $this->model );

		$this->model = $this->setup_model( $this->get_schema(), $model );
	}


	/**
	 * Parse options
	 *
	 * Will loop through the options setting the active options, render attributes
	 * and custom css
	 *
	 * @param Option[]             $schema The options schema that will be parsed
	 * @param array<string, mixed> $model  The current saved values
	 * @param int|null             $index  The index of the model values in case of repeater options
	 *
	 * @return array<string, mixed> The saved values
	 */
	public function setup_model( $schema, $model, $index = null ) {
		foreach ( $schema as $option_id => $option_schema ) {
			$passed_dependency = $this->check_dependency( $option_schema, $model );

			// Don't proceed if we didn't passed dependency
			if ( ! $passed_dependency ) {
				continue;
			}

			// Group options don't store the value so we need to look at children
			if ( isset( $option_schema->is_layout ) && $option_schema->is_layout ) {
				if ( $option_schema->child_options ) {
					$model = array_merge( $model, $this->setup_model( $option_schema->child_options, $model ) );
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
				if ( ! empty( $option_schema->child_options ) ) {
					if ( $option_schema->type === 'repeater' ) {
						if ( ! empty( $model[$option_id] ) && is_array( $model[$option_id] ) ) {
							foreach ( $model[$option_id] as $index => $option_value ) {
								$index                     = (int) $index;
								$model[$option_id][$index] = $this->setup_model( $option_schema->child_options, $option_value, $index );
							}

							// Reset index
							$index = null;
						}
					} else {
						$saved_value       = isset( $model[$option_id] ) ? $model[$option_id] : [];
						$model[$option_id] = $this->setup_model( $option_schema->child_options, $saved_value );

						if ( ! empty( $saved_value ) ) {
							$model[$option_id] = $saved_value;
						}
					}
				}
			}
		}

		return $model;
	}

	/**
	 * Will check if a current option passes dependency
	 *
	 * @param Option $option_schema The option schema
	 * @param mixed  $model         The saved value
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
	 * @param array<string, mixed> $dependency_config The dependency configuration
	 * @param array<string, mixed> $model             The saved value
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
		} elseif ( $validation_type === 'not_in' && ! in_array( $value, $dependency_config['value'], true ) ) {
			return true;
		} elseif ( $validation_type === 'value_set' && $value !== null ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the current stack model
	 *
	 * @return array<string, mixed> The saved value for this schema
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
		$path       = explode( '.', $path );
		$temp_model = &$this->model;

		foreach ( $path as $key ) {
			if ( ! isset( $temp_model[$key] ) ) {
				return null;
			}

			$temp_model = &$temp_model[$key];
		}

		return $temp_model;
	}

	/**
	 * Get value
	 *
	 * Returns the value for an option if model is provided
	 *
	 * @param string $path          A dot separated string
	 * @param mixed  $default_value The default value to return in case the option value is not found
	 *
	 * @return mixed|null
	 */
	public function get_value( $path, $default_value = null ) {
		$value = $this->get_value_from_path( $path );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		return $default_value;
	}

	public function set_value( $path, $new_value ) {
		$path_locations = explode( '.', $path );
		$active_model   = &$this->model;
		$paths_count    = count( $path_locations );
		$i              = 1;

		foreach ( $path_locations as $path_location ) {
			if ( ! is_array( $active_model ) ) {
				$active_model = [];
			}

			if ( $i === $paths_count ) {
				$active_model[$path_location] = $new_value;
			} else {
				$active_model[$path_location] = [];
			}

			$i++;
		}
	}
}
