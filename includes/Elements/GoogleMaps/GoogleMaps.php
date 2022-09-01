<?php

namespace ZionBuilder\Elements\GoogleMaps;

use ZionBuilder\Elements\Element;
use ZionBuilder\Utils;
use ZionBuilder\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class GoogleMaps extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'google_maps';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Google Maps', 'zionbuilder' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'external';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'maps', 'google', 'location', 'position', 'mapbox', 'region', 'search', 'tooltip', 'pin', 'direction', 'localization', 'geo' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-map';
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'location',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Map Location', 'zionbuilder' ),
				'default'     => 'Chicago',
				'description' => esc_html__( 'Enter the desired location for this map', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'zoom',
			[
				'type'        => 'slider',
				'description' => esc_html__( 'Set the map zoom.' ),
				'title'       => esc_html__( 'Zoom level', 'zionbuilder' ),
				'default'     => 15,
				'min'         => 1,
				'max'         => 21,
			]
		);

		$options->add_option(
			'height',
			[
				'type'                    => 'dynamic_slider',
				'description'             => esc_html__( 'Set the map height.' ),
				'title'                   => esc_html__( 'Map height', 'zionbuilder' ),
				'default'                 => '400px',
				'show_responsive_buttons' => true,
				'sync'                    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.height',
				'options'                 => [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'pt',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'em',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'vh',
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
			]
		);

		$options->add_option(
			'map_type',
			[
				'type'        => 'custom_selector',
				'description' => __( 'Set the map type.' ),
				'title'       => __( 'Map type', 'zionbuilder' ),
				'default'     => 'normal',
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Normal', 'zionbuilder' ),
						'id'   => 'normal',
					],
					[
						'name' => __( 'Terrain', 'zionbuilder' ),
						'id'   => 'terrain',
					],
				],
			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Plugin::instance()->scripts->get_script_url( 'elements/GoogleMaps/editor', 'js' ) );
	}

	/**
	 * Enqueue element styles for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_style(), enqueue_element_style() functions
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		// Using helper methods will go through caching policy
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/GoogleMaps/frontend.css' ) );
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$location   = rawurldecode( $options->get_value( 'location', 'Chicago' ) );
		$type       = $options->get_value( 'map_type', 'normal' ) === 'normal' ? '' : 'k';
		$iframe_src = sprintf(
			'https://www.google.com/maps?api=1&q=%s&z=%s&output=embed&t=%s',
			$location,
			$options->get_value( 'zoom', '15' ),
			$type
		);

		printf( '<iframe src="%s" frameborder="0" allowfullscreen="true"></iframe>', esc_attr( $iframe_src ) );
	}
}
