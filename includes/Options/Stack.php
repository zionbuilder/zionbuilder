<?php

namespace ZionBuilder\Options;

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
class Stack {
	public function add_to_stack( $option_id, $option_config, &$stack ) {
		if ( $option_config instanceof Option ) {
			$stack[$option_id] = $option_config;
			return $stack[$option_id];
		} else {
			if ( is_array( $option_config ) ) {
				$stack[$option_id] = new Option( $option_id, $option_config );
				return $stack[$option_id];
			}
		}

		_doing_it_wrong( 'Stack', esc_html__( 'Second argument of add_to_stack must be an array or an instance of ZionBuilder\Options\Option', 'zionbuilder' ), '1.0.0' );
		return null;
	}

	public function remove_from_stack( $option_id, &$stack ) {
		if ( isset( $stack[$option_id] ) ) {
			unset( $stack[$option_id] );
			return true;
		}

		return false;
	}

	public function add_option( $option_id, $option_config = [] ) {
		_doing_it_wrong( 'Stack', esc_html__( 'You need to implement add_option into your derived class.', 'zionbuilder' ), '1.0.0' );
	}

	public function add_group( $id, $config ) {
		$defaults = [
			'is_layout' => true,
		];

		return $this->add_option( $id, wp_parse_args( $config, $defaults ) );
	}
}
