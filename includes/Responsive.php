<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Install
 *
 * @package ZionBuilder
 */
class Responsive {
	const SETTINGS_OPTION_KEY = '_zionbuilder_breakpoints';

	public function __construct() {
		add_action( 'zionbuilder/page/save', [ $this, 'on_page_save' ] );
	}

	public static function save_breakpoints( $breakpoints = [] ) {
		return update_option( self::SETTINGS_OPTION_KEY, wp_json_encode( $breakpoints ) );
	}

	public function on_page_save( $params ) {
		if ( isset( $params['breakpoints'] ) ) {
			self::save_breakpoints( $params['breakpoints'] );
		}
	}

	public static function get_default_breakpoints() {
		return [
			[
				'name'      => __( 'Desktop', 'zionbuilder' ),
				'id'        => 'default',
				'icon'      => 'desktop',
				'icon'      => 'desktop',
				'builtIn'   => true,
				'isDefault' => true,
			],
			[
				'name'    => __( 'Laptop', 'zionbuilder' ),
				'id'      => 'laptop',
				'width'   => 991,
				'height'  => 768,
				'icon'    => 'laptop',
				'builtIn' => true,
			],
			[
				'name'    => __( 'Tablet', 'zionbuilder' ),
				'id'      => 'tablet',
				'width'   => 767,
				'height'  => 1024,
				'icon'    => 'tablet',
				'builtIn' => true,
			],
			[
				'name'    => __( 'Mobile', 'zionbuilder' ),
				'id'      => 'mobile',
				'width'   => 575,
				'height'  => 667,
				'icon'    => 'mobile',
				'builtIn' => true,
			],
		];
	}


	/**
	 * Returns the list of saved breakpoints
	 *
	 * @return array
	 */
	public static function get_breakpoints() {
		$breakpoints = get_option( self::SETTINGS_OPTION_KEY, json_encode( self::get_default_breakpoints() ) );

		return json_decode( $breakpoints, true );
	}
}
