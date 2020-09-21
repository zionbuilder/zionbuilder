<?php

namespace ZionBuilder;

use ZionBuilder\Elements\Style;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * CSS classes
 *
 * @package ZionBuilder
 */
class CSSClasses {
	const CLASSES_OPTION_KEY = '_zionbuilder_css_classes';

	/**
	 * Holds a cached version of the css classes
	 *
	 * @var array<int, array{id: string, name: string, style: array<string, mixed>}>
	 */
	private static $cached_css_classes = null;

	/**
	 * Save the css classes to DB
	 *
	 * @param array<int, array{id: string, name: string, style: array<string, mixed>}> $classes
	 *
	 * @return bool
	 */
	public static function save_classes( $classes = [] ) {
		// Clear the cache
		Plugin::instance()->cache->delete_dynamic_css_cache();
		// Also clear the cache
		return update_option( self::CLASSES_OPTION_KEY, wp_json_encode( $classes ) );
	}


	/**
	 * Get saved css classes from DB
	 *
	 * @return array<int, array{id: string, name: string, style: array<string, mixed>}> The css classes saved in DB
	 */
	public static function get_classes() {
		if ( null === self::$cached_css_classes ) {
			$saved_css_classes        = get_option( self::CLASSES_OPTION_KEY );
			self::$cached_css_classes = json_decode( $saved_css_classes, true );
		}

		return self::$cached_css_classes;
	}


	/**
	 * Returns the css for the saved css classes
	 *
	 * @return string The CSS for the saved CSS classes
	 */
	public static function get_css() {
		$css         = '';
		$css_classes = self::get_classes();

		if ( is_array( $css_classes ) ) {
			foreach ( $css_classes as $key => $class_config ) {
				if ( ! empty( $class_config['style'] ) && isset( $class_config['id'] ) ) {
					$class_selector = '.zb .' . $class_config['id'];
					$css           .= Style::get_styles( $class_selector, $class_config['style'] );
				}
			}
		}

		return $css;
	}
}
