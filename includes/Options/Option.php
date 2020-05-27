<?php

namespace ZionBuilder\Options;

use ZionBuilder\Options\Stack;

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
class Option extends Stack {
	/**
	 * Holds a refference to the option config
	 *
	 * @var string The option id
	 */
	public $id = null;

	/**
	 * Holds a refference to the option type
	 *
	 * @var string The option type
	 */
	public $type = null;

	/**
	 * Holds a list of child Options
	 *
	 * @var Option[] The list of child options
	 */
	public $child_options = [];

	/**
	 * Main class constructor
	 *
	 * @param string $option_id The option ID
	 * @param array $option_config List of options that will be added on class instantiation
	 */
	public function __construct( $option_id, $option_config = [] ) {
		$this->id = $option_id;

		$ignored_properties = [
			'child_options',
		];

		foreach ( $option_config as $key => $value ) {
			if ( ! in_array( $key, $ignored_properties, true ) ) {
				$this->$key = $value;
			}
		}

		if ( ! empty( $option_config['child_options'] ) && is_array( $option_config['child_options'] ) ) {
			foreach ( $option_config['child_options'] as $option_id => $option_config ) {
				$this->add_option( $option_id, $option_config );
			}
		}
	}

	/**
	 * Returns the option ID
	 *
	 * @return string The current option ID
	 */
	public function get_option_id() {
		return $this->id;
	}

	public function &get_stack() {
		return $this->child_options;
	}
}
