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
	 */
	public $id   = null;
	public $type = null;

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

	public function get_option_id() {
		return $this->id;
	}

	public function add_option( $option_id, $option_config = [] ) {
		if ( ! property_exists( $this, 'child_options' ) ) {
			$this->child_options = [];
		}

		return $this->add_to_stack( $option_id, $option_config, $this->child_options );
	}

	public function remove_option( $option_id ) {
		return $this->remove_from_stack( $option_id, $this->child_options );
	}

	public function replace_option( $option_id, $option_config ) {
		return $this->add_option( $option_id, $option_config );
	}

}
