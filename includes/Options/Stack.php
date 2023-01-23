<?php

namespace ZionBuilder\Options;

use ZionBuilder\Options\Option;

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
 *
 */
class Stack {

	/**
	 * Returns the list of options
	 *
	 * @return array The list of options
	 */
	public function &get_stack() {
		_doing_it_wrong( 'Stack', esc_html__( 'The child class must implement the get_stack() method returning a reference to an array', 'zionbuilder' ), '1.0.0' );
		return [];
	}

	/**
	 * Adds a new option to stack
	 *
	 * @param string $option_id The option ID that will be added as child
	 * @param Option|array $option_config
	 *
	 * @return Option|boolean Returns the newly added child option or false on failure
	 */
	public function add_option( $option_id, $option_config = [] ) {
		$stack = &$this->get_stack();

		if ( $option_config instanceof Option ) {
			$stack[$option_id] = $option_config;
			return $stack[$option_id];
		} elseif ( is_array( $option_config ) ) {
			$stack[$option_id] = new Option( $option_id, $option_config );
			return $stack[$option_id];
		}
	}

	/**
	 * Removes an option from the option stack
	 *
	 * @param string $option_id The option ID that will be added to stack
	 *
	 * @return boolean True if the option was succesfully removed. False in case the option ID is not registered on the provided stack
	 */
	public function remove_option( $option_id ) {
		$stack = &$this->get_stack();

		if ( isset( $stack[$option_id] ) ) {
			unset( $stack[$option_id] );
			return true;
		}

		return false;
	}


	/**
	 * Replaces an option in stack
	 *
	 * @param string $option_id The option ID that will be added as child
	 * @param Option|array $option_config
	 *
	 * @return Option|boolean Returns the newly added child option or false on failure
	 */
	public function replace_option( $option_id, $option_config ) {
		return $this->add_option( $option_id, $option_config );
	}

	/**
	 * Adds a new option that acts as a group option.
	 * Group options do not store the value inside the option ID. It can be used when you
	 * want to group options inside accordions/menus without saving the full option path
	 * as ID.
	 *
	 * @param string $id The option ID that will be registered on stack
	 * @param Option|array $config An instance of Option or an array configuration
	 *
	 * @return Option A new instance of the option with provided configuration
	 */
	public function add_group( $id, $config ) {
		$defaults = [
			'is_layout' => true,
		];

		$stack = &$this->get_stack();

		if ( isset( $stack[$id] ) ) {
			return $stack[$id];
		}

		return $this->add_option( $id, wp_parse_args( $config, $defaults ) );
	}

	/**
	 * Get option
	 *
	 * Returns a single option from the stack
	 *
	 * @param mixed $option_path The path to the option you want to retrieve
	 *
	 * @return Option|null The requested option or null on failure
	 */
	public function &get_option( $option_path ) {
		$path_locations = explode( '.', $option_path );
		$paths_count    = count( $path_locations );
		$stack_as_clone = $this->get_stack();
		$i              = 1;

		foreach ( $path_locations as $path_location ) {
			if ( ! array_key_exists( $path_location, $stack_as_clone ) ) {
				return null;
			}

			if ( $i === $paths_count ) {
				return $stack_as_clone[$path_location];
			}

			if ( ! isset( $stack_as_clone[$path_location]->child_options ) ) {
				return null;
			}

			$stack_as_clone = $stack_as_clone[$path_location]->child_options;
			$i++;
		}

		return null;
	}

}
