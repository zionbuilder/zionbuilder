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

	public static $caches_deviecs = null;

	public static $responsive_devices_as_device_width = null;

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
		if ( null === self::$caches_deviecs ) {
			$saved_breakpoints = get_option( self::SETTINGS_OPTION_KEY );

			if ( false === $saved_breakpoints ) {
				self::$caches_deviecs = self::get_default_breakpoints();
			} else {
				self::$caches_deviecs = json_decode( $saved_breakpoints, true );
			}
		}

		return self::$caches_deviecs;
	}

	public static function get_breakpoints_as_device_width() {
		if ( null === self::$caches_deviecs ) {
			$saved_breakpoints = self::get_breakpoints();
			$breakpoints       = [];

			foreach ( $saved_breakpoints as $device_config ) {
				// Use 9999 as value for the default so we can order the values properly
				$breakpoints[$device_config['id']] = $device_config['id'] === 'default' ? 9999999 : $device_config['width'];
			}

			// Sort by width
			uasort(
				$breakpoints,
				function( $a, $b ) {
					return ( $a > $b ) ? -1 : 1;
				}
			);

			self::$responsive_devices_as_device_width = $breakpoints;

		}

		return self::$responsive_devices_as_device_width;
	}
}
