<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Icons
 *
 * @package ZionBuilder
 */
class Icons {
	private $icon_list      = null;
	private $icon_locations = null;

	/**
	 * Icons constructor.
	 */
	public function __construct() {
		// Add icons css to dynamic file
		add_filter( 'zionbuilder/cache/dynamic_css', [ $this, 'add_styles_to_dynamic_css' ] );
	}

	/**
	 * Returns the list of all icons
	 */
	public function get_icons_list() {
		if ( null !== $this->icon_list ) {
			return $this->icon_list;
		}

		$icon_locations = $this->get_icons_locations();
		$all_icons      = [];
		foreach ( $icon_locations as $icon_pack_config ) {
			$pack_icons = FileSystem::get_file_system()->get_contents( $icon_pack_config['path'] . 'icons.json' );
			if ( $pack_icons ) {
				$icons_config = json_decode( $pack_icons, true );
				if ( $icons_config ) {
					$all_icons[] = $icons_config;
				}
			}
		}

		$this->icon_list = $all_icons;
		return $all_icons;
	}

	public function get_icon_package_data( $icon_set_id ) {
		$icons_locations = $this->get_icons_locations();

		if ( isset( $icons_locations[$icon_set_id] ) ) {
			return $icons_locations[$icon_set_id];
		}

		return false;
	}

	private function get_builtin_icons() {
		return [
			'FontAwesome5Free-Regular'   => [
				'name'      => 'Font Awesome 5 Free Regular',
				'id'        => 'FontAwesome5Free-Regular',
				'url'       => Utils::get_file_url( 'assets/icons/FontAwesome5Free-Regular/', 'relative' ),
				'path'      => Utils::get_file_path( 'assets/icons/FontAwesome5Free-Regular/' ),
				'file_name' => 'fa-regular-400',
				'built_in'  => true,
			],
			'FontAwesome5Free-Solid'     => [
				'name'      => 'Font Awesome 5 Free Solid',
				'id'        => 'FontAwesome5Free-Solid',
				'url'       => Utils::get_file_url( 'assets/icons/FontAwesome5Free-Solid/', 'relative' ),
				'path'      => Utils::get_file_path( 'assets/icons/FontAwesome5Free-Solid/' ),
				'file_name' => 'fa-solid-900',
				'built_in'  => true,
			],
			'FontAwesome5Brands-Regular' => [
				'name'      => 'Font Awesome 5 Brands Regular',
				'id'        => 'FontAwesome5Brands-Regular',
				'url'       => Utils::get_file_url( 'assets/icons/FontAwesome5Brands-Regular/', 'relative' ),
				'path'      => Utils::get_file_path( 'assets/icons/FontAwesome5Brands-Regular/' ),
				'file_name' => 'fa-brands-400',
				'built_in'  => true,
			],
		];
	}

	/**
	 * Get icons locations
	 *
	 * Will return a list of icon locations
	 *
	 * @param bool $use_cache
	 *
	 * @return mixed|void|null
	 */
	public function get_icons_locations( $use_cache = true ) {
		if ( $use_cache && null !== $this->icon_locations ) {
			return $this->icon_locations;
		}

		// User custom locations
		$this->icon_locations = apply_filters( 'zionbuilder/icons/locations', $this->get_builtin_icons() );

		return $this->icon_locations;
	}


	/**
	 * Returns a specific icon unicode
	 *
	 * @param string $icon The icon config
	 *
	 * @return string
	 */
	public static function get_icon( $icon ) {
		if ( strpos( $icon, 'u' ) === 0 ) {
			$icon = json_decode( '"\\' . $icon . '"' );
		}
		return $icon;
	}


	/**
	 * Returns the icon attributes as list
	 *
	 * @param array $icon The icon config
	 *
	 * @return array The icon HTML attributes as list
	 */
	public static function get_icon_attributes( $icon ) {
		if ( ! is_array( $icon ) || empty( $icon['family'] ) || empty( $icon['unicode'] ) ) {
			return [];
		}

		return [
			'data-znpbiconfam' => $icon['family'],
			'data-znpbicon'    => self::get_icon( $icon['unicode'] ),
		];
	}


	/**
	 * Returns the HTML attributes for a specific icon
	 *
	 * @param array $icon The icon config
	 *
	 * @return string The HTML attributes required to render the icon
	 */
	public static function get_icon_attribute_string( $icon ) {
		$result = '';
		if ( ! is_array( $icon ) ) {
			return $result;
		}
		if ( empty( $icon['family'] ) || empty( $icon['unicode'] ) ) {
			return $result;
		}

		return 'data-znpbiconfam="' . $icon['family'] . '" data-znpbicon="' . self::get_icon( $icon['unicode'] ) . '"';
	}

	/**
	 * Adds styles for icons to dynamic css file
	 *
	 * @param string $css The current css for the dynamic css
	 *
	 * @return string Original css plus the icons css
	 */
	public function add_styles_to_dynamic_css( $css ) {
		$css .= $this->get_icons_css();

		return $css;
	}


	/**
	 * Returns the css for the icons
	 *
	 * @return string
	 */
	public function get_icons_css() {
		$icons_locations = $this->get_icons_locations();
		$output          = '
		[data-znpbicon]:before {
			content: attr(data-znpbicon)
		}';
		foreach ( $icons_locations as $font_data ) {
			$output .= $this->get_icon_package_css( $font_data );
		}
		return $output;
	}

	/**
	 * Returns the css for a specific icon config
	 *
	 * @param array $icon_package_config The icon config
	 *
	 * @return string The css based on the config provided
	 */
	public function get_icon_package_css( $icon_package_config ) {
		$font_name = esc_html( $icon_package_config['name'] );

		$icon_file            = $icon_package_config['url'] . $icon_package_config['file_name'];
		$normalized_font_name = preg_replace( '/\s+/', '_', $font_name );
		return "
			@font-face {
				font-family: \"{$font_name}\"; font-weight: normal; font-style: normal;
				src: url(\"{$icon_file}.eot\");
				src: url(\"{$icon_file}.eot#iefix\") format(\"embedded-opentype\"),
				url(\"{$icon_file}.woff\") format(\"woff\"),
				url(\"{$icon_file}.ttf\") format(\"truetype\"),
				url(\"{$icon_file}.svg#{$normalized_font_name}\") format(\"svg\");
				font-style: normal;
				font-weight: 400;
				font-display: block;
			}
			[data-znpbiconfam=\"{$font_name}\"]:before, [data-znpbiconfam=\"{$font_name}\"] {
				font-family: \"{$font_name}\";
				font-weight: 400;
			}
		";
	}
}
