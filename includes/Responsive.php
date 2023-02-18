<?php

namespace ZionBuilder;

use ZionBuilder\Plugin;

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

	public static $cached_devices = null;

	public static $responsive_devices_as_device_width = null;

	public function __construct() {
	}

	public static function save_breakpoints( $breakpoints = [] ) {
		return update_option( self::SETTINGS_OPTION_KEY, wp_json_encode( $breakpoints ) );
	}

	public static function get_default_breakpoints() {
		return [
			[
				'name'      => __( 'Desktop', 'zionbuilder' ),
				'id'        => 'default',
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
		if ( null === self::$cached_devices ) {
			$saved_breakpoints = get_option( self::SETTINGS_OPTION_KEY );
			if ( false === $saved_breakpoints ) {
				self::$cached_devices = self::get_default_breakpoints();
			} else {
				self::$cached_devices = json_decode( $saved_breakpoints, true );
			}
		}

		return self::$cached_devices;
	}

	public static function get_breakpoints_as_device_width() {
		if ( null === self::$responsive_devices_as_device_width ) {
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

	public static function get_breakpoints_mobile_first() {
		$breakpoints              = self::get_breakpoints_as_device_width();
		$mobile_first_breakpoints = [];

		// Reverse sort breakpoints
		uasort(
			$breakpoints,
			function( $a, $b ) {
				return ( $a > $b ) ? 1 : -1;
			}
		);

		$last_device_width = 0;
		foreach ( $breakpoints as $device => $device_width ) {
			if ( $device === 'mobile' ) {
				$mobile_first_breakpoints[$device] = 0;
			} else {
				$mobile_first_breakpoints[$device] = $last_device_width + 1;
			}

			$last_device_width = $device_width;
		}

		// Sort again in normal order
		uasort(
			$mobile_first_breakpoints,
			function( $a, $b ) {
				return ( $a > $b ) ? -1 : 1;
			}
		);

		return $mobile_first_breakpoints;
	}
	public static function replace_devices_in_css( $css ) {
		$devices_config = self::get_breakpoints_as_device_width();
		$devices_map    = [ '__ZIONBUILDER_LAPTOP__', '__ZIONBUILDER_TABLET__', '__ZIONBUILDER_MOBILE_' ];
		$width_map      = [ sprintf( '%spx', $devices_config['laptop'] ), sprintf( '%spx', $devices_config['tablet'] ), sprintf( '%spx', $devices_config['mobile'] ) ];

		return str_replace( $devices_map, $width_map, $css );
	}
}
